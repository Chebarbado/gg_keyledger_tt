<?php

namespace App\Services;

use App\Models\DeliveryAttempt;
use App\Models\LicenseKey;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

/**
 * Заглушка поставщика. name = a|b, поведение из .env
 */
class FakeSupplier
{
    public function __construct(public string $name) {}

    public function issue(string $requestId, Order $order): array
    {
        $prev = DeliveryAttempt::where('request_id', $requestId)
            ->where('supplier', $this->name)
            ->first();

        if ($prev) {
            return $prev->status === 'ok'
                ? ['status' => 'ok', 'code' => $prev->code]
                : ['status' => 'error', 'reason' => $prev->reason ?: 'supplier_error'];
        }

        $failRate = (float) config("marketplace.supplier_{$this->name}_failure_rate", 0);
        $timeoutRate = (float) config("marketplace.supplier_{$this->name}_timeout_rate", 0);

        // sleep специально — чтобы было похоже на реальный таймаут
        if (random_int(1, 100) <= $timeoutRate * 100) {
            sleep((int) config('marketplace.supplier_timeout_seconds', 3));

            return ['status' => 'error', 'reason' => 'timeout'];
        }

        if (random_int(1, 100) <= $failRate * 100) {
            $this->saveAttempt($requestId, $order, 'error', null, 'supplier_error');

            return ['status' => 'error', 'reason' => 'supplier_error'];
        }

        return DB::transaction(function () use ($requestId, $order) {
            $code = $this->takeKey($order);

            if (! $code) {
                $this->saveAttempt($requestId, $order, 'error', null, 'out_of_stock');

                return ['status' => 'error', 'reason' => 'out_of_stock'];
            }

            $this->saveAttempt($requestId, $order, 'ok', $code);

            return ['status' => 'ok', 'code' => $code];
        });
    }

    private function takeKey(Order $order): ?string
    {
        $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

        if ($order->issued_code) {
            return $order->issued_code; // уже выдали — не трогаем
        }

        // lockForUpdate, иначе два запроса схватят один ключ
        $key = LicenseKey::where('status', 'available')
            ->orderBy('id')
            ->lockForUpdate()
            ->first();

        if (! $key) {
            return null;
        }

        $key->update([
            'status' => 'assigned',
            'order_id' => $order->id,
            'assigned_at' => now(),
        ]);

        $order->update(['issued_code' => $key->code]);

        return $key->code;
    }

    private function saveAttempt(string $requestId, Order $order, string $status, ?string $code, ?string $reason = null): void
    {
        DeliveryAttempt::create([
            'request_id' => $requestId,
            'order_id' => $order->id,
            'supplier' => $this->name,
            'status' => $status,
            'code' => $code,
            'reason' => $reason,
        ]);
    }
}
