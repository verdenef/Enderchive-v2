<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserGameProgress>
 */
class UserGameProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userGame = \App\Models\UserGame::factory()->create();
        
        // Get a milestone for this game, or create one
        $milestone = \App\Models\GameMilestone::where('game_id', $userGame->game_id)->first();
        if (!$milestone) {
            $milestone = \App\Models\GameMilestone::factory()->create([
                'game_id' => $userGame->game_id,
            ]);
        }

        return [
            'user_id' => $userGame->user_id,
            'game_id' => $userGame->game_id,
            'user_game_id' => $userGame->user_game_id,
            'milestone_id' => $milestone->milestone_id,
            'achieved_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'notes' => $this->faker->optional(0.4)->sentence(),
            'evidence_url' => $this->faker->optional(0.2)->url(),
        ];
    }
}

