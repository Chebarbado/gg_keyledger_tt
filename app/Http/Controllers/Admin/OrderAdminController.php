<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderAdminController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $this->checkToken($request);

        $orders = Order::with('product')
            ->whereIn('status', ['out_of_stock', 'delivery_failed', 'paid', 'delivering'])
            ->where(function ($q) {
                $q->whereNull('issued_code')
                    ->orWhereIn('status', ['out_of_stock', 'delivery_failed']);
            })
            ->orderByDesc('id')
            ->get();

        if ($request->expectsJson()) {
            return response()->json(['data' => $orders]);
        }

        return view('admin.orders.index', compact('orders'));
    }

    public function retry(Request $request, string $publicId, OrderService $orders): JsonResponse
    {
        $this->checkToken($request);

        $order = Order::where('public_id', $publicId)->firstOrFail();
        $order = $orders->retryDelivery($order);

        return response()->json([
            'order_id' => $order->public_id,
            'status' => $order->status,
            'issued_code' => $order->issued_code,
        ]);
    }

    private function checkToken(Request $request): void
    {
        // auth
        $token = config('marketplace.admin_token');
        if ($request->header('X-Admin-Token') !== $token && $request->query('token') !== $token) {
            abort(403, 'Invalid admin token');
        }
    }
}
