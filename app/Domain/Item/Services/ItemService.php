<?php

namespace App\Domain\Item\Services;

use App\Domain\Item\Models\Item;

class ItemService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Item
    {
        return Item::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Item $item, array $data): Item
    {
        $item->update($data);

        return $item;
    }

    public function delete(Item $item): void
    {
        $item->delete();
    }
}
