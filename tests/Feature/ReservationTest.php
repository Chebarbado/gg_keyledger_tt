<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_reserves_and_decrements_stock(): void
    {
        $this->seed();

        $product = Product::where('sku', 'KEY-CS2-PRIME')->firstOrFail();
        $before = $product->stock;

        $order = app(OrderService::class)->create('KEY-CS2-PRIME');

        $this->assertSame('reserved', $order->status);
        $this->assertNotNull($order->reserved_until);
        $this->assertSame($before - 1, $product->fresh()->stock);
    }

    public function test_expire_returns_stock(): void
    {
        $this->seed();

        $product = Product::where('sku', 'KEY-CS2-PRIME')->firstOrFail();
        $before = $product->stock;

        $order = app(OrderService::class)->create('KEY-CS2-PRIME');
        $order->update(['reserved_until' => now()->subSecond()]);

        $expired = app(OrderService::class)->expireReservations();

        $this->assertSame(1, $expired);
        $this->assertSame('expired', $order->fresh()->status);
        $this->assertSame($before, $product->fresh()->stock);
    }

    public function test_pay_after_expire_is_rejected(): void
    {
        $this->seed();

        $order = app(OrderService::class)->create('KEY-CS2-PRIME');
        $order->update(['reserved_until' => now()->subSecond()]);
        app(OrderService::class)->expireReservations();

        $this->postJson('/api/orders/'.$order->public_id.'/pay', ['result' => 'paid'])
            ->assertStatus(409)
            ->assertJsonPath('code', 'reservation_expired');
    }
}
