<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductBroadcast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function update(Request $request, string $sku, ProductBroadcast $broadcast, OrderService $orders): JsonResponse
    {
        $token = (string) $request->query('token', $request->header('X-Admin-Token', ''));
        if (! hash_equals((string) config('marketplace.admin_token'), $token)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'price' => ['sometimes', 'integer', 'min:1'],
            'stock' => ['sometimes', 'integer', 'min:0'],
        ]);

        $product = Product::where('sku', $sku)->firstOrFail();
        $product->update($data);
        $product->refresh();

        // пока заказ в брони — подтягиваем новую цену к оплате
        if (array_key_exists('price', $data)) {
            Order::query()
                ->where('sku', $sku)
                ->where('status', 'reserved')
                ->orderBy('id')
                ->each(fn (Order $order) => $orders->syncPriceIfReserved($order));
        }

        $event = $broadcast->bump($product);

        return response()->json(['product' => $product, 'event' => $event]);
    }
}
