<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductBroadcast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductBroadcast $broadcast,
        private OrderService $orders,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->orders->expireReservations();

        $q = trim((string) $request->query('q', ''));
        $type = trim((string) $request->query('type', ''));

        $query = Product::query()->orderBy('id');

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', '%'.$q.'%')
                    ->orWhere('sku', 'like', '%'.$q.'%');
            });
        }

        if ($type !== '') {
            $query->where('type', $type);
        }

        $products = $query->get()->map(fn (Product $p) => [
            'sku' => $p->sku,
            'name' => $p->name,
            'type' => $p->type,
            'price' => $p->price,
            'currency' => $p->currency,
            'image' => $p->image,
            'stock' => $p->stock,
            'available' => $p->stock > 0,
        ]);

        return response()->json([
            'version' => $this->broadcast->version(),
            'data' => $products,
        ]);
    }

    /** инкрементальные апдейты для витрины (polling) */
    public function changes(Request $request): JsonResponse
    {
        $this->orders->expireReservations();

        $since = (int) $request->query('since', 0);

        return response()->json([
            'version' => $this->broadcast->version(),
            'events' => $this->broadcast->since($since),
        ]);
    }
}
