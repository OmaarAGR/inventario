<?php

namespace App\Domain\Order\Exceptions;

use App\Domain\Item\Models\Item;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsufficientStockException extends Exception
{
    public static function forItem(Item $item, int $requested): self
    {
        return new self("Insufficient stock for item [{$item->sku}]: requested {$requested}, available {$item->stock}.");
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], 422);
    }
}
