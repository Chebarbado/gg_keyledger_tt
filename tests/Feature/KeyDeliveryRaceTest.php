<?php

namespace Tests\Feature;

use App\Models\LicenseKey;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeyDeliveryRaceTest extends TestCase
{
    use RefreshDatabase;

    // 50 раз один event_id — если тут падает, смотреть lockForUpdate в takeKey
    public function test_parallel_paid_webhooks_issue_single_key(): void
    {
        $this->seed();

        $order = app(OrderService::class)->create('KEY-GTA5');
        $payload = [
            'event_id' => 'evt_parallel',
            'order_id' => $order->public_id,
            'status' => 'paid',
            'amount' => $order->final_amount,
            'currency' => 'RUB',
            'created_at' => now()->toIso8601String(),
        ];

        // в phpunit последовательно, но локи всё равно должны держать
        for ($i = 0; $i < 50; $i++) {
            $this->postJson('/webhook/payment', $payload)->assertOk();
        }

        $order->refresh();

        $this->assertSame('delivered', $order->status);
        $this->assertNotNull($order->issued_code);
        $this->assertSame(1, LicenseKey::where('order_id', $order->id)->count());
    }
}
