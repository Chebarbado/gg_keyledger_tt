<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestsPageController extends Controller
{
    public function __invoke(Request $request): View
    {
        $token = config('marketplace.admin_token');
        if ($request->header('X-Admin-Token') !== $token && $request->query('token') !== $token) {
            abort(403, 'Invalid admin token');
        }

        $products = Product::query()->orderBy('id')->get();

        return view('tests.index', [
            'products' => $products,
            'adminToken' => $token,
        ]);
    }
}
