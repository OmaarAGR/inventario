<?php

use App\Domain\Order\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);
Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
