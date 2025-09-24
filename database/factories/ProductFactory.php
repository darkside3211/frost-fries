<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 50, 500), // Price between 50.00 and 500.00
            'stock_quantity' => $this->faker->numberBetween(5, 100),
            'sku' => $this->faker->unique()->ean8(),
        ];
    }
}