<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameMilestone>
 */
class GameMilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $milestoneTypes = [
            ['code' => 'tutorial_complete', 'name' => 'Complete Tutorial', 'points' => 10],
            ['code' => 'first_boss', 'name' => 'Defeat First Boss', 'points' => 50],
            ['code' => 'mid_game', 'name' => 'Reach Mid-Game', 'points' => 100],
            ['code' => 'final_boss', 'name' => 'Defeat Final Boss', 'points' => 200],
            ['code' => 'main_story', 'name' => 'Complete Main Story', 'points' => 300],
            ['code' => 'side_quests', 'name' => 'Complete All Side Quests', 'points' => 150],
            ['code' => 'collectibles', 'name' => 'Collect All Items', 'points' => 100],
            ['code' => 'achievement', 'name' => 'Unlock All Achievements', 'points' => 250],
        ];

        $milestone = $this->faker->randomElement($milestoneTypes);

        return [
            'game_id' => \App\Models\Game::factory(),
            'code' => $milestone['code'] . '_' . $this->faker->unique()->numberBetween(1000, 9999),
            'name' => $milestone['name'],
            'description' => $this->faker->optional(0.7)->sentence(),
            'points' => $milestone['points'],
            'sequence' => $this->faker->optional(0.8)->numberBetween(1, 10),
            'is_optional' => $this->faker->boolean(30), // 30% chance of being optional
        ];
    }
}

