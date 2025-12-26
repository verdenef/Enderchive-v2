<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserGame>
 */
class UserGameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['Wishlist', 'Playing', 'Completed', 'Abandoned'];

        return [
            'user_id' => \App\Models\User::factory(),
            'game_id' => \App\Models\Game::factory(),
            'status' => $this->faker->randomElement($statuses),
            'rating' => $this->faker->optional(0.7)->numberBetween(1, 10),
            'playtime_hours' => $this->faker->numberBetween(0, 500),
        ];
    }
}
