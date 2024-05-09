<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductReview>
 */
class ProductReviewFactory extends Factory
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
            "product_id" => fake()->numberBetween(1,5),
            "product_review_comment" => fake()->words(5, true),
            "product_review_rating" => fake()->numberBetween(1,5),
        ];
    }
}
