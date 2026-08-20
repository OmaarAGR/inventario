<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
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
