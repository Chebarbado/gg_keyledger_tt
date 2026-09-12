<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

/**
 * Простой pub/sub через cache: один процесс пишет версию+событие,
 * вкладки забирают через polling. Для artisan serve надёжнее SSE.
 */
class ProductBroadcast
{
    private const VERSION_KEY = 'marketplace.catalog.version';
    private const EVENTS_KEY = 'marketplace.catalog.events';

    public function bump(Product $product, string $type = 'product.updated'): array
    {
        $event = [
            'type' => $type,
            'sku' => $product->sku,
            'price' => (int) $product->price,
            'stock' => (int) $product->stock,
            'available' => $product->stock > 0,
            'name' => $product->name,
            'updated_at' => now()->toIso8601String(),
        ];

        $version = (int) Cache::get(self::VERSION_KEY, 0) + 1;
        Cache::forever(self::VERSION_KEY, $version);

        $events = Cache::get(self::EVENTS_KEY, []);
        $events[] = $event + ['version' => $version];
        // не раздуваем бесконечно
        if (count($events) > 100) {
            $events = array_slice($events, -100);
        }
        Cache::forever(self::EVENTS_KEY, $events);

        return $event + ['version' => $version];
    }

    public function version(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 0);
    }

    /** события новее since (version) */
    public function since(int $since): array
    {
        $events = Cache::get(self::EVENTS_KEY, []);

        return array_values(array_filter(
            $events,
            fn (array $e) => ($e['version'] ?? 0) > $since,
        ));
    }
}
