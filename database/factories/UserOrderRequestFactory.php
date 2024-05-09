<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserOrderRequest>
 */
class UserOrderRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => 2,
            "user_cart_id" => fake()->numberBetween(1,5),
            "user_contact_id" => fake()->numberBetween(1,5),
            "user_order_request_status_id" => 1,
            "user_order_request_note" => fake()->implode(" ", fake()->words()),
            "user_order_request_due_date" => fake()->dateTime(),
            "user_order_request_reason" => fake()->words(5,true),
            "user_order_total_price" => fake()->numberBetween(100000,500000),
            "user_order_total_product" => fake()->numberBetween(1,10),
            "user_order_total_quantity" => fake()->numberBetween(1.10),
        ];
    }
}
