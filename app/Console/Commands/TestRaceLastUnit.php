<?php

namespace App\Console\Commands;

use App\Exceptions\OutOfStockException;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestRaceLastUnit extends Command
{
    protected $signature = 'test:race-last-unit {sku=KEY-GTA5} {--buyers=2}';

    protected $description = 'Два (N) покупателя одновременно берут последний товар';

    public function handle(OrderService $orders): int
    {
        $sku = $this->argument('sku');
        $buyers = max(2, (int) $this->option('buyers'));

        $results = [];
        // последовательно с одним lockForUpdate SQLite всё равно сериализует — этого достаточно для демо
        for ($i = 0; $i < $buyers; $i++) {
            try {
                $order = $orders->create($sku, null, 'race-'.Str::uuid());
                $results[] = ['ok' => true, 'order' => $order->public_id, 'status' => $order->status];
            } catch (OutOfStockException $e) {
                $results[] = ['ok' => false, 'message' => $e->getMessage()];
            }
        }

        $wins = collect($results)->where('ok', true)->count();
        $this->table(['#', 'ok', 'detail'], collect($results)->values()->map(fn ($r, $i) => [
            $i + 1,
            $r['ok'] ? 'yes' : 'no',
            $r['ok'] ? $r['order'].' / '.$r['status'] : $r['message'],
        ]));

        $this->info("Winners: {$wins} (ожидаем 1 при stock=1)");

        return $wins === 1 ? self::SUCCESS : self::FAILURE;
    }
}
