<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserContact>
 */
class UserContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "is_active" => 1,
            "user_id" => fake()->numberBetween(2,10),
            "user_address" => fake()->address(),
            "user_phone_number" => fake()->phoneNumber(),
        ];
    }
}
