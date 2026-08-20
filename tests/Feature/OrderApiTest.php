<?php

use App\Enums\OrderStatus;
use App\Models\Item;
use App\Models\Order;

it('creates an order with items and computes the total', function () {
    $item = Item::factory()->create(['price' => 10, 'stock' => 50]);

    $response = $this->postJson('/api/orders', [
        'customer_name' => 'Jane Doe',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 3],
        ],
    ])->assertCreated();

    $response
        ->assertJsonPath('data.total', '30.00')
        ->assertJsonPath('data.status', OrderStatus::Pending->value);
});

it('confirms an order and decrements stock', function () {
    $item = Item::factory()->create(['stock' => 5]);
    $order = Order::factory()->create();
    $order->items()->create([
        'item_id' => $item->id,
        'quantity' => 3,
        'unit_price' => $item->price,
        'subtotal' => $item->price * 3,
    ]);

    $this->postJson("/api/orders/{$order->id}/confirm")
        ->assertOk()
        ->assertJsonPath('data.status', OrderStatus::Confirmed->value);

    expect($item->fresh()->stock)->toBe(2);
});

it('rejects confirming an order without enough stock', function () {
    $item = Item::factory()->create(['stock' => 1]);
    $order = Order::factory()->create();
    $order->items()->create([
        'item_id' => $item->id,
        'quantity' => 3,
        'unit_price' => $item->price,
        'subtotal' => $item->price * 3,
    ]);

    $this->postJson("/api/orders/{$order->id}/confirm")->assertStatus(422);

    expect($item->fresh()->stock)->toBe(1);
});

it('cancels a confirmed order and restocks items', function () {
    $item = Item::factory()->create(['stock' => 10]);
    $order = Order::factory()->confirmed()->create();
    $order->items()->create([
        'item_id' => $item->id,
        'quantity' => 4,
        'unit_price' => $item->price,
        'subtotal' => $item->price * 4,
    ]);
    $item->decrement('stock', 4);

    $this->postJson("/api/orders/{$order->id}/cancel")
        ->assertOk()
        ->assertJsonPath('data.status', OrderStatus::Cancelled->value);

    expect($item->fresh()->stock)->toBe(10);
});

it('rejects confirming an already cancelled order', function () {
    $order = Order::factory()->cancelled()->create();

    $this->postJson("/api/orders/{$order->id}/confirm")->assertStatus(422);
});
