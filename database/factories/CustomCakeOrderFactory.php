<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomCakeOrder>
 */
class CustomCakeOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => fake()->numberBetween(2,5),
            "custom_cake_order_status_id" => fake()->numberBetween(1,4),
            "custom_cake_order_price" => fake()->numberBetween(100000, 1000000),
            "custome_cake_order_design_theme" => fake()->implode(" ", fake()->words()),
            "custome_cake_order_color" => fake()->colorName(),
            "custome_cake_order_size" => fake()->implode(" ", fake()->words()),
            "custome_cake_order_due_date" => fake()->dateTime(),
            "custome_cake_order_response" => fake()->implode(" ", fake()->words()),
        ];
    }
}
