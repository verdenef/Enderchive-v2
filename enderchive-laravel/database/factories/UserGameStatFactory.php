<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserGameStat>
 */
class UserGameStatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userGame = \App\Models\UserGame::factory()->create();

        return [
            'user_id' => $userGame->user_id,
            'game_id' => $userGame->game_id,
            'user_game_id' => $userGame->user_game_id,
            'hours_played' => $this->faker->optional(0.8)->randomFloat(1, 0.5, 500.0),
            'percent_override' => $this->faker->optional(0.3)->numberBetween(0, 100),
        ];
    }
}

