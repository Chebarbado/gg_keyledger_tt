<?php

namespace App\Console\Commands;

use App\Models\PromoCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TestRacePromoCodes extends Command
{
    protected $signature = 'test:race-promo {code=LIMIT3} {--attempts=10}';

    protected $description = 'Параллельно создаёт заказы с одним промокодом';

    public function handle(): int
    {
        $code = strtoupper($this->argument('code'));
        $attempts = (int) $this->option('attempts');
        $promo = PromoCode::where('code', $code)->firstOrFail();

        // реально параллельно — serve должен быть запущен
        $responses = Http::pool(function ($pool) use ($attempts, $code) {
            $reqs = [];
            for ($i = 0; $i < $attempts; $i++) {
                $reqs[] = $pool->asJson()->post(url('/api/orders'), [
                    'sku' => 'KEY-CS2-PRIME',
                    'promo_code' => $code,
                    'idempotency_key' => 'promo-race-'.Str::uuid(),
                ]);
            }

            return $reqs;
        });

        $ok = collect($responses)->filter(fn ($r) => $r->status() === 201)->count();
        $promo->refresh();

        $this->info("created={$ok} used={$promo->used_count}/{$promo->max_uses}");

        return self::SUCCESS;
    }
}
