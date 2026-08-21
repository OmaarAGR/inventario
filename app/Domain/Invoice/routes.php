<?php

use App\Domain\Invoice\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::apiResource('invoices', InvoiceController::class)->only(['index', 'store', 'show']);
Route::post('invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
