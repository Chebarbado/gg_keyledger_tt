<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        $products = Product::query()->orderBy('id')->limit(5)->get();

        return view('home', compact('products'));
    }
}
