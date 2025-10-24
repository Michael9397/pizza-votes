<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'user_id' => User::factory(),
            'dimension' => fake()->randomElement(['taste', 'service', 'atmosphere', 'value']),
            'score' => fake()->numberBetween(1, 5),
            'notes' => fake()->optional(0.5)->sentence(),
            'visited_at' => fake()->dateTimeThisYear(),
        ];
    }
}
