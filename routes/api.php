<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{publicId}', [OrderController::class, 'show']);
Route::post('/orders/{publicId}/pay', [OrderController::class, 'pay']);
