<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LastUnitRaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_one_buyer_gets_last_unit(): void
    {
        $this->seed();

        Product::where('sku', 'KEY-GTA5')->update(['stock' => 1]);

        $wins = 0;
        $losses = 0;

        // последовательно под одним lockForUpdate — имитируем гонку без настоящего параллелизма
        for ($i = 0; $i < 2; $i++) {
            $response = $this->postJson('/api/orders', [
                'sku' => 'KEY-GTA5',
                'idempotency_key' => 'race-'.$i,
            ]);

            if ($response->status() === 201) {
                $wins++;
                $this->assertSame('reserved', $response->json('status'));
            } elseif ($response->status() === 409) {
                $losses++;
                $this->assertSame('out_of_stock', $response->json('code'));
            }
        }

        $this->assertSame(1, $wins);
        $this->assertSame(1, $losses);
        $this->assertSame(0, Product::where('sku', 'KEY-GTA5')->value('stock'));
    }

    public function test_service_race_same_result(): void
    {
        $this->seed();
        Product::where('sku', 'KEY-GTA5')->update(['stock' => 1]);

        $service = app(OrderService::class);
        $ok = 0;
        $fail = 0;

        for ($i = 0; $i < 2; $i++) {
            try {
                $service->create('KEY-GTA5', null, 'svc-race-'.$i);
                $ok++;
            } catch (\App\Exceptions\OutOfStockException) {
                $fail++;
            }
        }

        $this->assertSame(1, $ok);
        $this->assertSame(1, $fail);
    }
}
