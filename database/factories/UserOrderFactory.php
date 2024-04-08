<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserOrder>
 */
class UserOrderFactory extends Factory
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
            "user_contact_id" => fake()->numberBetween(1,20),
            "order_note" => fake()->words(10, true),
            "order_total_price" => fake()->numberBetween(1000,2000000),
            "order_total_product" => fake()->numberBetween(1,100),
            "order_total_quantity" => fake()->numberBetween(1,1000),
            "order_due_date" => fake()->dateTime(),
            "order_payment_status" => "paid",
            "order_status" => "process",
            "order_reason" => fake()->words(10,true),
        ];
    }
}
