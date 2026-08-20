<?php

namespace Database\Factories;

use App\Domain\Item\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####??')),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'stock' => $this->faker->numberBetween(0, 200),
            'price' => $this->faker->randomFloat(2, 1, 500),
        ];
    }
}
