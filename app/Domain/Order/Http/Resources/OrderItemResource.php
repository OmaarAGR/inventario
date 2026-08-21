<?php

namespace App\Domain\Order\Http\Resources;

use App\Domain\Order\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin OrderItem */
class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'item_sku' => $this->whenLoaded('item', fn () => $this->item->sku),
            'item_name' => $this->whenLoaded('item', fn () => $this->item->name),
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'subtotal' => $this->subtotal,
        ];
    }
}
