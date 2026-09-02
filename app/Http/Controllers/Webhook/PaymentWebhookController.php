<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, OrderService $orders): JsonResponse
    {
        // контракт, лишние поля не трогаем
        $data = $request->validate([
            'event_id' => ['required', 'string'],
            'order_id' => ['required', 'string'],
            'status' => ['required', 'in:paid,failed'],
            'amount' => ['required', 'integer'],
            'currency' => ['required', 'string', 'size:3'],
            'created_at' => ['nullable', 'date'],
        ]);

        return response()->json($orders->handleWebhook($data));
    }
}
