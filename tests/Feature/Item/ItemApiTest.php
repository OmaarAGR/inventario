<?php

use App\Domain\Item\Models\Item;

it('lists items', function () {
    Item::factory()->count(3)->create();

    $this->getJson('/api/items')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('creates an item', function () {
    $this->postJson('/api/items', [
        'sku' => 'SKU-1',
        'name' => 'Widget',
        'stock' => 10,
        'price' => 9.99,
    ])
        ->assertCreated()
        ->assertJsonPath('data.sku', 'SKU-1')
        ->assertJsonPath('data.stock', 10);

    $this->assertDatabaseHas('items', ['sku' => 'SKU-1']);
});

it('rejects a duplicate sku', function () {
    Item::factory()->create(['sku' => 'SKU-1']);

    $this->postJson('/api/items', [
        'sku' => 'SKU-1',
        'name' => 'Widget',
        'stock' => 10,
        'price' => 9.99,
    ])->assertJsonValidationErrors('sku');
});

it('updates an item', function () {
    $item = Item::factory()->create(['stock' => 5]);

    $this->putJson("/api/items/{$item->id}", ['stock' => 25])
        ->assertOk()
        ->assertJsonPath('data.stock', 25);
});

it('deletes an item', function () {
    $item = Item::factory()->create();

    $this->deleteJson("/api/items/{$item->id}")->assertNoContent();

    $this->assertDatabaseMissing('items', ['id' => $item->id]);
});
