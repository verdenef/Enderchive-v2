<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserGame;
use App\Models\UserGameStat;

class UserGameStatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userGames = UserGame::all();

        foreach ($userGames as $userGame) {
            // Only create stats for games that are not in Wishlist
            if ($userGame->status === 'Wishlist') {
                continue;
            }

            // 60% chance of having stats
            if (rand(1, 100) <= 60) {
                // Calculate hours based on status
                $hoursPlayed = match($userGame->status) {
                    'Completed' => rand(10, 200),
                    'Playing' => rand(5, 100),
                    'Abandoned' => rand(1, 50),
                    default => rand(0, 20),
                };

                UserGameStat::firstOrCreate(
                    [
                        'user_id' => $userGame->user_id,
                        'game_id' => $userGame->game_id,
                    ],
                    [
                        'user_game_id' => $userGame->user_game_id,
                        'hours_played' => $hoursPlayed,
                        'percent_override' => rand(1, 100) <= 20 ? rand(0, 100) : null,
                    ]
                );
            }
        }
    }
}

