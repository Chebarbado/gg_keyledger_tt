<?php

namespace App\Http\Controllers;

use App\Services\OrderService;

class OrderPageController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function show(string $publicId)
    {
        $order = $this->orderService->findByPublicId($publicId);

        return view('orders.show', compact('order'));
    }
}
