<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserCartDetail>
 */
class UserCartDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_cart_id" => fake()->numberBetween(1,2),
            "product_id" => fake()->numberBetween(1,5),
            "cart_detail_quantity" => fake()->numberBetween(1,10),
        ];
    }
}
