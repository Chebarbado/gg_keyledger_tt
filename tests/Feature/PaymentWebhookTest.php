<?php

namespace Tests\Feature;

use App\Models\LicenseKey;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    // главный кейс из тз — один event_id, два ключа быть не должно
    public function test_duplicate_event_id_is_idempotent(): void
    {
        $this->seed();

        $order = app(OrderService::class)->create('KEY-CS2-PRIME');
        $payload = [
            'event_id' => 'evt_duplicate',
            'order_id' => $order->public_id,
            'status' => 'paid',
            'amount' => $order->final_amount,
            'currency' => 'RUB',
            'created_at' => now()->toIso8601String(),
        ];

        $this->postJson('/webhook/payment', $payload)->assertOk();
        $this->postJson('/webhook/payment', $payload)->assertOk(); // второй раз — no-op

        $order->refresh();
        $this->assertSame('delivered', $order->status);
        $this->assertNotNull($order->issued_code);
        $this->assertSame(1, LicenseKey::where('order_id', $order->id)->count()); // вот это главное
    }

    // вебхук раньше заказа — из тз, не забыть processPending
    public function test_webhook_before_order_is_processed_later(): void
    {
        $this->seed();

        $payload = [
            'event_id' => 'evt_early',
            'order_id' => 'ord_future123', // заказа ещё нет
            'status' => 'paid',
            'amount' => 1290,
            'currency' => 'RUB',
            'created_at' => now()->toIso8601String(),
        ];

        $this->postJson('/webhook/payment', $payload)->assertOk()->assertJson(['pending' => true]);

        $product = Product::where('sku', 'KEY-CS2-PRIME')->firstOrFail();
        $order = Order::create([
            'public_id' => 'ord_future123',
            'product_id' => $product->id,
            'sku' => $product->sku,
            'amount' => $product->price,
            'discount_amount' => 0,
            'final_amount' => $product->price,
            'currency' => 'RUB',
            'status' => 'created',
        ]);

        // как в OrderController::store после create
        app(OrderService::class)->processPendingForOrder($order);

        $order->refresh();
        $this->assertSame('delivered', $order->status);
        $this->assertNotNull($order->issued_code);
    }
}
