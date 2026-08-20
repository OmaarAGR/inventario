<?php

namespace App\Exceptions;

use App\Models\Order;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvalidOrderStateException extends Exception
{
    public static function forTransition(Order $order, string $action): self
    {
        return new self("Cannot {$action} order #{$order->id} while it is in status [{$order->status->value}].");
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], 422);
    }
}
