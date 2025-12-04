<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true), // e.g. "Delicious Spicy Burger"
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 50, 500), // Prices between 50.00 and 500.00
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'sku' => $this->faker->unique()->ean8(), // Random 8-digit barcode
        ];
    }
}