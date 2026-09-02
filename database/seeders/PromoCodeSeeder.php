<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $promos = [
            ['code' => 'WELCOME10', 'type' => 'percent', 'value' => 10, 'max_uses' => 100, 'currency' => null],
            ['code' => 'GG500', 'type' => 'amount', 'value' => 500, 'max_uses' => 20, 'currency' => 'RUB'],
            ['code' => 'LIMIT3', 'type' => 'percent', 'value' => 25, 'max_uses' => 3, 'currency' => null],
            ['code' => 'ONCEONLY', 'type' => 'percent', 'value' => 50, 'max_uses' => 1, 'currency' => null],
        ];

        foreach ($promos as $promo) {
            PromoCode::query()->updateOrCreate(
                ['code' => $promo['code']],
                $promo,
            );
        }
    }
}
