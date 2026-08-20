<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory()->confirmed(),
            'invoice_number' => strtoupper('INV-'.$this->faker->unique()->numerify('######')),
            'status' => InvoiceStatus::Draft,
            'total' => $this->faker->randomFloat(2, 10, 5000),
            'issued_at' => now(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Paid,
            'paid_at' => now(),
        ]);
    }
}
