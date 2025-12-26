<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\GameMilestone;

class GameMilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = Game::all();

        foreach ($games as $game) {
            // 60% chance of having milestones
            if (rand(1, 100) <= 60) {
                $milestones = [
                    [
                        'code' => 'tutorial_complete',
                        'name' => 'Complete Tutorial',
                        'description' => 'Finish the tutorial section',
                        'points' => 10,
                        'sequence' => 1,
                        'is_optional' => false,
                    ],
                    [
                        'code' => 'first_boss',
                        'name' => 'Defeat First Boss',
                        'description' => 'Defeat the first major boss',
                        'points' => 50,
                        'sequence' => 2,
                        'is_optional' => false,
                    ],
                    [
                        'code' => 'mid_game',
                        'name' => 'Reach Mid-Game',
                        'description' => 'Progress to the middle of the game',
                        'points' => 100,
                        'sequence' => 3,
                        'is_optional' => false,
                    ],
                    [
                        'code' => 'final_boss',
                        'name' => 'Defeat Final Boss',
                        'description' => 'Defeat the final boss',
                        'points' => 200,
                        'sequence' => 4,
                        'is_optional' => false,
                    ],
                    [
                        'code' => 'main_story',
                        'name' => 'Complete Main Story',
                        'description' => 'Finish the main storyline',
                        'points' => 300,
                        'sequence' => 5,
                        'is_optional' => false,
                    ],
                    [
                        'code' => 'side_quests',
                        'name' => 'Complete All Side Quests',
                        'description' => 'Finish all optional side quests',
                        'points' => 150,
                        'sequence' => null,
                        'is_optional' => true,
                    ],
                    [
                        'code' => 'collectibles',
                        'name' => 'Collect All Items',
                        'description' => 'Find and collect all collectible items',
                        'points' => 100,
                        'sequence' => null,
                        'is_optional' => true,
                    ],
                ];

                // Create 3-6 random milestones per game
                shuffle($milestones);
                $selectedMilestones = array_slice($milestones, 0, rand(3, 6));
                
                foreach ($selectedMilestones as $milestone) {
                    GameMilestone::firstOrCreate(
                        [
                            'game_id' => $game->game_id,
                            'code' => $milestone['code'],
                        ],
                        [
                            'name' => $milestone['name'],
                            'description' => $milestone['description'],
                            'points' => $milestone['points'],
                            'sequence' => $milestone['sequence'],
                            'is_optional' => $milestone['is_optional'],
                        ]
                    );
                }
            }
        }
    }
}

