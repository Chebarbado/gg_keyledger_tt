<?php

namespace App\Console\Commands;

use App\Models\LicenseKey;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestRaceWebhooks extends Command
{
    protected $signature = 'test:race-webhooks {order_id} {--count=50} {--event-id=} {--parallel}';

    protected $description = 'Дёргает вебхук много раз по одному заказу';

    public function handle(OrderService $orders): int
    {
        $order = Order::where('public_id', $this->argument('order_id'))->firstOrFail();
        $count = (int) $this->option('count');
        $eventId = $this->option('event-id') ?: 'evt_race_'.uniqid();

        $payload = [
            'event_id' => $eventId,
            'order_id' => $order->public_id,
            'status' => 'paid',
            'amount' => $order->final_amount,
            'currency' => $order->currency,
            'created_at' => now()->toIso8601String(),
        ];

        if ($this->option('parallel')) {
            // нужен php artisan serve в другом окне
            Http::pool(fn ($pool) => collect(range(1, $count))->map(
                fn () => $pool->asJson()->post(url('/webhook/payment'), $payload),
            )->all());
        } else {
            // без сервера — напрямую в сервис, удобнее для локальной проверки
            for ($i = 0; $i < $count; $i++) {
                $orders->handleWebhook($payload);
            }
        }

        $order->refresh();

        $this->info("status={$order->status} code=".($order->issued_code ?: '-').' keys='.LicenseKey::where('order_id', $order->id)->count());

        return self::SUCCESS;
    }
}
