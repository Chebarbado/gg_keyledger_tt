<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductBroadcast;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogRealtimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_snapshot_includes_stock_and_version(): void
    {
        $this->seed();

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonStructure(['version', 'data'])
            ->assertJsonFragment(['sku' => 'KEY-GTA5']);
    }

    public function test_bump_emits_change_event(): void
    {
        $this->seed();

        $before = app(ProductBroadcast::class)->version();

        $this->artisan('products:bump', [
            'sku' => 'KEY-GTA5',
            '--stock' => 0,
            '--price' => 999,
        ])->assertSuccessful();

        $this->getJson('/api/products/changes?since='.$before)
            ->assertOk()
            ->assertJsonPath('events.0.sku', 'KEY-GTA5')
            ->assertJsonPath('events.0.stock', 0)
            ->assertJsonPath('events.0.price', 999);
    }

    public function test_reserved_order_price_follows_bump(): void
    {
        $this->seed();

        $order = app(OrderService::class)->create('KEY-CS2-PRIME');
        $old = $order->final_amount;

        Product::where('sku', 'KEY-CS2-PRIME')->update(['price' => $old + 100]);
        app(OrderService::class)->syncPriceIfReserved($order->fresh());

        $order->refresh();
        $this->assertSame($old + 100, $order->amount);
        $this->assertSame($old + 100, $order->final_amount);
    }
}
