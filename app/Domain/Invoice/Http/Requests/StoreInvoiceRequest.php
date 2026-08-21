<?php

namespace App\Domain\Invoice\Http\Requests;

use App\Domain\Order\Enums\OrderStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders', 'id')->where('status', OrderStatus::Confirmed->value),
            ],
        ];
    }
}
