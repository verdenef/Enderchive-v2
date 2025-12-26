<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserGame;
use App\Models\UserGameProgress;
use App\Models\GameMilestone;

class UserGameProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userGames = UserGame::all();

        foreach ($userGames as $userGame) {
            // Get milestones for this game
            $milestones = GameMilestone::where('game_id', $userGame->game_id)->get();
            
            if ($milestones->isEmpty()) {
                continue;
            }

            // Users who are Playing or Completed are more likely to have progress
            $hasProgress = match($userGame->status) {
                'Completed' => rand(1, 100) <= 90, // 90% chance
                'Playing' => rand(1, 100) <= 70,    // 70% chance
                default => rand(1, 100) <= 20,     // 20% chance for others
            };

            if ($hasProgress) {
                // Completed games should have more milestones achieved
                $numMilestones = match($userGame->status) {
                    'Completed' => rand(ceil($milestones->count() * 0.8), $milestones->count()),
                    'Playing' => rand(1, ceil($milestones->count() * 0.6)),
                    default => rand(0, ceil($milestones->count() * 0.3)),
                };

                $selectedMilestones = $milestones->random(min($numMilestones, $milestones->count()));
                
                foreach ($selectedMilestones as $milestone) {
                    // Make sure milestone belongs to the game
                    if ($milestone->game_id !== $userGame->game_id) {
                        continue;
                    }

                    // Use firstOrCreate to avoid duplicates
                    UserGameProgress::firstOrCreate(
                        [
                            'user_id' => $userGame->user_id,
                            'game_id' => $userGame->game_id,
                            'milestone_id' => $milestone->milestone_id,
                        ],
                        [
                            'user_game_id' => $userGame->user_game_id,
                            'achieved_at' => now()->subDays(rand(0, 365)),
                            'notes' => rand(1, 100) <= 30 ? 'Achieved this milestone!' : null,
                            'evidence_url' => rand(1, 100) <= 10 ? 'https://example.com/screenshot.jpg' : null,
                        ]
                    );
                }
            }
        }
    }
}

