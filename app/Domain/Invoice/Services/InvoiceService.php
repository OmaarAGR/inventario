<?php

namespace App\Domain\Invoice\Services;

use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Order\Enums\OrderStatus;
use App\Domain\Order\Exceptions\InvalidOrderStateException;
use App\Domain\Order\Models\Order;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * @throws InvalidOrderStateException
     */
    public function generateFromOrder(Order $order): Invoice
    {
        if ($order->status !== OrderStatus::Confirmed) {
            throw InvalidOrderStateException::forTransition($order, 'invoice');
        }

        return DB::transaction(function () use ($order) {
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => $this->nextInvoiceNumber($order),
                'status' => InvoiceStatus::Draft,
                'total' => $order->total,
                'issued_at' => now(),
            ]);

            $order->update(['status' => OrderStatus::Invoiced]);

            return $invoice->load('order');
        });
    }

    public function markPaid(Invoice $invoice): Invoice
    {
        if ($invoice->status !== InvoiceStatus::Draft) {
            throw InvalidOrderStateException::forTransition($invoice->order()->firstOrFail(), 'mark as paid');
        }

        $invoice->update([
            'status' => InvoiceStatus::Paid,
            'paid_at' => now(),
        ]);

        return $invoice;
    }

    private function nextInvoiceNumber(Order $order): string
    {
        return sprintf('INV-%s-%06d', now()->format('Ymd'), $order->id);
    }
}
