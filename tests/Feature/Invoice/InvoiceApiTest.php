<?php

use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Order\Enums\OrderStatus;
use App\Domain\Order\Models\Order;

it('generates an invoice from a confirmed order', function () {
    $order = Order::factory()->confirmed()->create(['total' => 150]);

    $this->postJson('/api/invoices', ['order_id' => $order->id])
        ->assertCreated()
        ->assertJsonPath('data.total', '150.00')
        ->assertJsonPath('data.status', InvoiceStatus::Draft->value);

    expect($order->fresh()->status)->toBe(OrderStatus::Invoiced);
});

it('rejects generating an invoice from a pending order', function () {
    $order = Order::factory()->create();

    $this->postJson('/api/invoices', ['order_id' => $order->id])
        ->assertJsonValidationErrors('order_id');
});

it('marks an invoice as paid', function () {
    $invoice = Invoice::factory()->create();

    $this->postJson("/api/invoices/{$invoice->id}/pay")
        ->assertOk()
        ->assertJsonPath('data.status', InvoiceStatus::Paid->value);
});

it('rejects paying an already paid invoice', function () {
    $invoice = Invoice::factory()->paid()->create();

    $this->postJson("/api/invoices/{$invoice->id}/pay")->assertStatus(422);
});
