<?php

namespace App\Domain\Invoice\Http\Resources;

use App\Domain\Invoice\Models\Invoice;
use App\Domain\Order\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Invoice */
class InvoiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'status' => $this->status,
            'total' => $this->total,
            'issued_at' => $this->issued_at,
            'paid_at' => $this->paid_at,
            'order' => new OrderResource($this->whenLoaded('order')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
