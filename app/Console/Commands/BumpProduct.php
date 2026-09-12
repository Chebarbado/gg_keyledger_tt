<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductBroadcast;
use Illuminate\Console\Command;

class BumpProduct extends Command
{
    protected $signature = 'products:bump {sku} {--price=} {--stock=}';

    protected $description = 'Меняет цену/остаток и пушит обновление на витрину';

    public function handle(ProductBroadcast $broadcast, OrderService $orders): int
    {
        $product = Product::where('sku', $this->argument('sku'))->firstOrFail();

        $updates = [];
        if ($this->option('price') !== null) {
            $updates['price'] = (int) $this->option('price');
        }
        if ($this->option('stock') !== null) {
            $updates['stock'] = max(0, (int) $this->option('stock'));
        }

        if ($updates === []) {
            $this->error('Укажи --price= и/или --stock=');

            return self::FAILURE;
        }

        $product->update($updates);
        $product->refresh();

        // открытые брони подтягиваем к новой цене, иначе на чекауте старая сумма
        if (array_key_exists('price', $updates)) {
            Order::query()
                ->where('sku', $product->sku)
                ->where('status', 'reserved')
                ->orderBy('id')
                ->each(fn (Order $order) => $orders->syncPriceIfReserved($order));
        }

        $event = $broadcast->bump($product);

        $this->info("Updated {$product->sku}: price={$event['price']} stock={$event['stock']}");

        return self::SUCCESS;
    }
}
