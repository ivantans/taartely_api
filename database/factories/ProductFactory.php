<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
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
            "user_id" => 1,
            "product_category_id" => fake()->numberBetween(1,5),
            "product_status_id" => 1,
            "product_name" => implode(" ", fake()->words()),
            "product_slug" => fake()->slug(),
            "product_price" => fake()->numberBetween(1000, 1000000),
            "product_composision" => implode(" ", fake()->words()),
            "product_description" => implode(" ", fake()->words()),
        ];
    }
}
