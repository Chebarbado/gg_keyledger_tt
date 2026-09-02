<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sku' => ['required', 'string'],
            'promo_code' => ['nullable', 'string'],
            'idempotency_key' => ['nullable', 'string', 'max:128'],
        ]);

        try {
            $order = $this->orders->create(
                $data['sku'],
                $data['promo_code'] ?? null,
                $data['idempotency_key'] ?? null,
            );
            $this->orders->processPendingForOrder($order);

            return response()->json($this->toArray($order), 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(string $publicId): JsonResponse
    {
        return response()->json($this->toArray($this->orders->findByPublicId($publicId)));
    }

    public function pay(Request $request, string $publicId): JsonResponse
    {
        $data = $request->validate([
            'result' => ['required', 'in:paid,failed'],
        ]);

        $order = $this->orders->findByPublicId($publicId);

        // эмуляция платежки: хук сами себе
        $payload = [
            'event_id' => 'evt_'.Str::lower(Str::random(12)),
            'order_id' => $order->public_id,
            'status' => $data['result'],
            'amount' => $order->final_amount,
            'currency' => $order->currency,
            'created_at' => now()->toIso8601String(),
        ];

        $response = Http::asJson()->timeout(10)->post(url('/webhook/payment'), $payload);

        if (! $response->successful()) {
            return response()->json(['message' => 'Webhook failed'], 500);
        }

        return response()->json([
            'webhook' => $response->json(),
            'order' => $this->toArray($order->fresh(['product', 'promoCode'])),
        ]);
    }

    private function toArray(Order $order): array
    {
        return [
            'order_id' => $order->public_id,
            'sku' => $order->sku,
            'status' => $order->status,
            'amount' => $order->amount,
            'discount_amount' => $order->discount_amount,
            'final_amount' => $order->final_amount,
            'currency' => $order->currency,
            'issued_code' => $order->issued_code,
            'product' => $order->relationLoaded('product') ? $order->product : null,
            'promo_code' => $order->relationLoaded('promoCode') ? $order->promoCode?->code : null,
        ];
    }
}
