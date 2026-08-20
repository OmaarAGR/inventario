<?php

use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::apiResource('items', ItemController::class);

Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);
Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

Route::apiResource('invoices', InvoiceController::class)->only(['index', 'store', 'show']);
Route::post('invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
