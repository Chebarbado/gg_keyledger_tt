<?php

use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderPageController;
use App\Http\Controllers\Webhook\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

//витрина
Route::get('/', HomeController::class);

//страница статуса заказа, сюда редирект после покупки
Route::get('/orders/{publicId}', [OrderPageController::class, 'show']);

//вход от платёжки
Route::post('/webhook/payment', PaymentWebhookController::class);

//зависшие заказы
Route::get('/admin/orders', [OrderAdminController::class, 'index']);

//ручная выдача
Route::post('/admin/orders/{publicId}/retry-delivery', [OrderAdminController::class, 'retry']);
