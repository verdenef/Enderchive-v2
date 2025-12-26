<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserGameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = \App\Models\Game::all();
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            // Each user has 5-15 games in their library
            $userGames = $games->random(min(max(5, $games->count() > 0 ? 5 : 0), 15));
            foreach ($userGames as $game) {
                \App\Models\UserGame::firstOrCreate(
                    [
                        'user_id' => $user->user_id,
                        'game_id' => $game->game_id,
                    ],
                    [
                        'status' => ['Wishlist', 'Playing', 'Completed', 'Abandoned'][array_rand(['Wishlist', 'Playing', 'Completed', 'Abandoned'])],
                        'rating' => rand(1, 100) <= 30 ? rand(1, 10) : null,
                        'playtime_hours' => rand(0, 100),
                    ]
                );
            }
        }
    }
}


