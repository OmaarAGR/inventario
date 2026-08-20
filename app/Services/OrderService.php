<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidOrderStateException;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'customer_name' => $data['customer_name'],
                'status' => OrderStatus::Pending,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($data['items'] as $line) {
                $item = Item::findOrFail((int) $line['item_id']);
                $subtotal = round($item->price * (int) $line['quantity'], 2);

                $order->items()->create([
                    'item_id' => $item->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $item->price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update(['total' => $total]);

            return $order->load('items.item');
        });
    }

    /**
     * @throws InsufficientStockException
     * @throws InvalidOrderStateException
     */
    public function confirm(Order $order): Order
    {
        if ($order->status !== OrderStatus::Pending) {
            throw InvalidOrderStateException::forTransition($order, 'confirm');
        }

        return DB::transaction(function () use ($order) {
            $orderItems = $order->items()->with('item')->lockForUpdate()->get();

            foreach ($orderItems as $orderItem) {
                if ($orderItem->item->stock < $orderItem->quantity) {
                    throw InsufficientStockException::forItem($orderItem->item, $orderItem->quantity);
                }
            }

            foreach ($orderItems as $orderItem) {
                $orderItem->item->decrement('stock', $orderItem->quantity);
            }

            $order->update([
                'status' => OrderStatus::Confirmed,
                'confirmed_at' => now(),
            ]);

            return $order->fresh(['items.item']);
        });
    }

    /**
     * @throws InvalidOrderStateException
     */
    public function cancel(Order $order): Order
    {
        if (! in_array($order->status, [OrderStatus::Pending, OrderStatus::Confirmed], true)) {
            throw InvalidOrderStateException::forTransition($order, 'cancel');
        }

        return DB::transaction(function () use ($order) {
            if ($order->status === OrderStatus::Confirmed) {
                foreach ($order->items()->with('item')->lockForUpdate()->get() as $orderItem) {
                    $orderItem->item->increment('stock', $orderItem->quantity);
                }
            }

            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
            ]);

            return $order->fresh(['items.item']);
        });
    }
}
