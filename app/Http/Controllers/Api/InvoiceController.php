<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoices) {}

    public function index(): AnonymousResourceCollection
    {
        return InvoiceResource::collection(Invoice::with('order')->latest()->paginate());
    }

    public function store(StoreInvoiceRequest $request): InvoiceResource
    {
        $order = Order::findOrFail($request->integer('order_id'));

        return InvoiceResource::make($this->invoices->generateFromOrder($order));
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        return InvoiceResource::make($invoice->load('order'));
    }

    public function pay(Invoice $invoice): InvoiceResource
    {
        return InvoiceResource::make($this->invoices->markPaid($invoice));
    }
}
