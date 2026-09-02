<?php

namespace App\Services;

use App\Models\LicenseKey;
use App\Models\Order;
use App\Models\PaymentEvent;
use App\Models\Product;
use App\Models\PromoCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OrderService
{
    public function create(string $sku, ?string $promoCode = null, ?string $idempotencyKey = null): Order
    {
        // двойной клик на купить — тот же ключ, тот же заказ
        if ($idempotencyKey) {
            $existing = Order::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        $product = Product::where('sku', $sku)->firstOrFail();

        return DB::transaction(function () use ($product, $promoCode, $idempotencyKey) {
            $amount = $product->price;
            $discount = 0;
            $promo = null;

            if ($promoCode) {
                $promo = PromoCode::where('code', strtoupper(trim($promoCode)))->lockForUpdate()->first();
                if (! $promo) {
                    throw new InvalidArgumentException('Invalid promo code.');
                }
                if ($promo->used_count >= $promo->max_uses) {
                    throw new InvalidArgumentException('Promo code usage limit reached.');
                }

                // проценты вниз, иначе копейки плывут
                $discount = $promo->type === 'percent'
                    ? (int) floor($amount * $promo->value / 100)
                    : min($promo->value, $amount);
            }

            $order = Order::create([
                'public_id' => 'ord_'.Str::lower(Str::random(10)),
                'product_id' => $product->id,
                'sku' => $product->sku,
                'amount' => $amount,
                'discount_amount' => $discount,
                'final_amount' => max(0, $amount - $discount),
                'currency' => $product->currency,
                'status' => 'created',
                'promo_code_id' => $promo?->id,
                'idempotency_key' => $idempotencyKey,
            ]);

            if ($promo) {
                // ещё раз под локом — на случай гонки
                $promo = PromoCode::whereKey($promo->id)->lockForUpdate()->firstOrFail();
                if ($promo->used_count >= $promo->max_uses) {
                    throw new InvalidArgumentException('Promo code usage limit reached.');
                }
                $promo->uses()->firstOrCreate(['order_id' => $order->id]);
                $promo->increment('used_count');
            }

            return $order->load('product');
        });
    }

    public function findByPublicId(string $publicId): Order
    {
        return Order::with(['product', 'promoCode'])
            ->where('public_id', $publicId)
            ->firstOrFail();
    }

    public function handleWebhook(array $payload): array
    {
        $eventId = $payload['event_id'];
        $orderPublicId = $payload['order_id'];

        // платёжка шлёт at-least-once — дубль просто игнорим
        $existing = PaymentEvent::where('event_id', $eventId)->first();
        if ($existing) {
            return [
                'accepted' => true,
                'duplicate' => true,
                'order_status' => $existing->order?->status,
            ];
        }

        return DB::transaction(function () use ($payload, $eventId, $orderPublicId) {
            $order = Order::where('public_id', $orderPublicId)->lockForUpdate()->first();

            $event = PaymentEvent::create([
                'event_id' => $eventId,
                'order_public_id' => $orderPublicId,
                'order_id' => $order?->id,
                'status' => $payload['status'],
                'amount' => (int) $payload['amount'],
                'currency' => $payload['currency'],
                'event_created_at' => isset($payload['created_at']) ? Carbon::parse($payload['created_at']) : now(),
                'processed' => false,
                'payload' => $payload,
            ]);

            // вебхук может прийти раньше создания заказа (в тз так написано)
            if (! $order) {
                return ['accepted' => true, 'pending' => true, 'message' => 'Order not found yet'];
            }

            return $this->applyPaymentEvent($event, $order);
        });
    }

    public function processPendingForOrder(Order $order): void
    {
        $events = PaymentEvent::where('order_public_id', $order->public_id)
            ->where('processed', false)
            ->orderBy('id')
            ->get();

        foreach ($events as $event) {
            DB::transaction(function () use ($event, $order) {
                $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
                $this->applyPaymentEvent($event, $locked);
            });
        }
    }

    public function deliver(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status === 'delivered' || $order->issued_code) {
                if ($order->status !== 'delivered') {
                    $order->update(['status' => 'delivered']);
                }

                return $order->fresh();
            }

            if (! in_array($order->status, ['paid', 'delivering', 'out_of_stock', 'delivery_failed'], true)) {
                return $order;
            }

            $order->update(['status' => 'delivering']);

            // сначала a, если не вышло — пробуем b
            foreach (['a', 'b'] as $name) {
                $supplier = new FakeSupplier($name);
                $result = $supplier->issue('req_'.$order->public_id.'-'.$name, $order->fresh());

                if ($result['status'] === 'ok' && ! empty($result['code'])) {
                    $order->update([
                        'status' => 'delivered',
                        'issued_code' => $result['code'],
                    ]);

                    return $order->fresh();
                }

                if (($result['reason'] ?? null) === 'out_of_stock') {
                    $order->update(['status' => 'out_of_stock']);

                    return $order->fresh();
                }
            }

            $order->update(['status' => 'delivery_failed']);

            return $order->fresh();
        });
    }

    public function retryDelivery(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status === 'delivered') {
                return $order;
            }

            if (! in_array($order->status, ['out_of_stock', 'delivery_failed'], true)) {
                return $order;
            }

            if ($order->issued_code) {
                $order->update(['status' => 'delivered']);

                return $order->fresh();
            }

            // из админки — без фейковых поставщиков, сразу из пула
            $key = LicenseKey::where('status', 'available')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (! $key) {
                $order->update(['status' => 'out_of_stock']);

                return $order->fresh();
            }

            $key->update([
                'status' => 'assigned',
                'order_id' => $order->id,
                'assigned_at' => now(),
            ]);

            $order->update([
                'status' => 'delivered',
                'issued_code' => $key->code,
            ]);

            return $order->fresh();
        });
    }

    private function applyPaymentEvent(PaymentEvent $event, Order $order): array
    {
        if ($event->processed) {
            return ['accepted' => true, 'duplicate' => true, 'order_status' => $order->status];
        }

        // уже финал — просто помечаем событие
        if (in_array($order->status, ['delivered', 'payment_failed'], true)) {
            $event->update(['processed' => true, 'order_id' => $order->id]);

            return ['accepted' => true, 'order_status' => $order->status];
        }

        if ($event->status === 'failed') {
            if ($order->status === 'created') {
                $order->update(['status' => 'payment_failed']);
            }
            $event->update(['processed' => true, 'order_id' => $order->id]);

            return ['accepted' => true, 'order_status' => $order->fresh()->status];
        }

        if ($event->status === 'paid') {
            if ($order->status === 'created') {
                $order->update(['status' => 'paid']);
            }
            $event->update(['processed' => true, 'order_id' => $order->id]);

            $delivered = $this->deliver($order->fresh());

            return ['accepted' => true, 'order_status' => $delivered->status];
        }

        $event->update(['processed' => true, 'order_id' => $order->id]);

        return ['accepted' => true, 'order_status' => $order->status];
    }
}
