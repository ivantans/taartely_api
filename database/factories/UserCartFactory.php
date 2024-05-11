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
            "user_id" => fake()->numberBetween(2,3),
            "total_product" => fake()->numberBetween(1,10),
            "total_quantity" => fake()->numberBetween(1,10),
            "total_price" => fake()->numberBetween(10000,100000)
        ];
    }
}
