<?php

use App\Domain\Item\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::apiResource('items', ItemController::class);
