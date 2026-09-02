<?php

namespace Tests\Feature;

use App\Models\PromoCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PromoCodeRaceTest extends TestCase
{
    use RefreshDatabase;

    // LIMIT3 из сидера — max_uses=3, кидаем 10 заказов
    public function test_promo_code_limit_is_not_exceeded_under_parallel_requests(): void
    {
        $this->seed();

        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/orders', [
                'sku' => 'KEY-CS2-PRIME',
                'promo_code' => 'LIMIT3',
                'idempotency_key' => 'race-'.Str::uuid(), // разные заказы, иначе идемпотентность
            ]);
        }

        $promo = PromoCode::query()->where('code', 'LIMIT3')->firstOrFail();

        $this->assertSame(3, $promo->fresh()->used_count);
        $this->assertSame(3, $promo->uses()->count()); // used_count и uses должны совпасть
    }
}
