<?php

namespace App\Http\Controllers;

use App\Services\OrderService;

class OrderPageController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function show(string $publicId)
    {
        $order = $this->orders->findByPublicId($publicId);

        $priceChangedFrom = null;
        if ($order->status === 'reserved' && $order->product && $order->product->price !== $order->amount) {
            $priceChangedFrom = $order->amount;
            $order = $this->orders->syncPriceIfReserved($order);
        }

        return view('orders.show', compact('order', 'priceChangedFrom'));
    }
}
