<?php

namespace Database\Factories;

use App\Domain\Item\Models\Item;
use App\Domain\Order\Models\Order;
use App\Domain\Order\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $unitPrice = $this->faker->randomFloat(2, 1, 500);

        return [
            'order_id' => Order::factory(),
            'item_id' => Item::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => round($quantity * $unitPrice, 2),
        ];
    }
}
