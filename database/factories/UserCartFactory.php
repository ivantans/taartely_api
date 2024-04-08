<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserCart>
 */
class UserCartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => fake()->numberBetween(2,10),
            "cart_note" => fake()->words(10,true),
            "cart_total_price" => fake()->numberBetween(1000,5000000),
            "cart_total_product" => fake()->numberBetween(1,10),
            "cart_total_quantity" => fake()->numberBetween(1,100),
            "cart_due_date" => fake()->dateTime(),
            "cart_hidden" => false,
        ];
    }
}
