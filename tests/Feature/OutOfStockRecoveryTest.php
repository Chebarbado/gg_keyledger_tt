<?php

namespace Tests\Feature;

use App\Models\LicenseKey;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutOfStockRecoveryTest extends TestCase
{
    use RefreshDatabase;

    // этап 3 — пул пустой, потом админ докидывает ключ
    public function test_empty_pool_moves_order_to_recoverable_state_and_manual_retry_works(): void
    {
        $this->seed();
        LicenseKey::query()->update(['status' => 'assigned']); // обнуляем пул

        $orders = app(OrderService::class);
        $order = $orders->create('KEY-EFT');

        $orders->handleWebhook([
            'event_id' => 'evt_oos',
            'order_id' => $order->public_id,
            'status' => 'paid',
            'amount' => $order->final_amount,
            'currency' => 'RUB',
            'created_at' => now()->toIso8601String(),
        ]);

        $order->refresh();
        $this->assertSame('out_of_stock', $order->status);
        $this->assertNull($order->issued_code);

        // как будто админ залил ключи
        LicenseKey::create([
            'code' => 'RECOVER-KEY-001',
            'status' => 'available',
        ]);

        $order = $orders->retryDelivery($order->fresh()); // без FakeSupplier, сразу из пула

        $this->assertSame('delivered', $order->status);
        $this->assertSame('RECOVER-KEY-001', $order->issued_code);
    }
}
