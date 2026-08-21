<?php

namespace App\Domain\Order\Http\Controllers;

use App\Domain\Order\Http\Requests\StoreOrderRequest;
use App\Domain\Order\Http\Resources\OrderResource;
use App\Domain\Order\Models\Order;
use App\Domain\Order\Services\OrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(): AnonymousResourceCollection
    {
        return OrderResource::collection(Order::with('items.item')->latest()->paginate());
    }

    public function store(StoreOrderRequest $request): OrderResource
    {
        $order = $this->orders->create($request->validated());

        return OrderResource::make($order);
    }

    public function show(Order $order): OrderResource
    {
        return OrderResource::make($order->load('items.item'));
    }

    public function confirm(Order $order): OrderResource
    {
        return OrderResource::make($this->orders->confirm($order));
    }

    public function cancel(Order $order): OrderResource
    {
        return OrderResource::make($this->orders->cancel($order));
    }
}
