<?php

namespace App\Domain\Item\Http\Controllers;

use App\Domain\Item\Http\Requests\StoreItemRequest;
use App\Domain\Item\Http\Requests\UpdateItemRequest;
use App\Domain\Item\Http\Resources\ItemResource;
use App\Domain\Item\Models\Item;
use App\Domain\Item\Services\ItemService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    public function __construct(private readonly ItemService $items) {}

    public function index(): AnonymousResourceCollection
    {
        return ItemResource::collection(Item::latest()->paginate());
    }

    public function store(StoreItemRequest $request): ItemResource
    {
        $item = $this->items->create($request->validated());

        return ItemResource::make($item);
    }

    public function show(Item $item): ItemResource
    {
        return ItemResource::make($item);
    }

    public function update(UpdateItemRequest $request, Item $item): ItemResource
    {
        $item = $this->items->update($item, $request->validated());

        return ItemResource::make($item);
    }

    public function destroy(Item $item): Response
    {
        $this->items->delete($item);

        return response()->noContent();
    }
}
