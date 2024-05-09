<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomCakeOrderImage>
 */
class CustomCakeOrderImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => fake()->numberBetween(1,5),
            "costume_cake_order_id" => fake()->numberBetween(1,5),
            "costume_cake_order_image_path" => fake()->imageUrl(),
        ];
    }
}
