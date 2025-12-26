<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100 games
        $games = \App\Models\Game::factory(100)->create();

        // Create relationships for each game
        foreach ($games as $game) {
            // Attach random platforms (1-3 platforms per game)
            $platforms = \App\Models\Platform::inRandomOrder()
                ->limit(rand(1, 3))
                ->get();
            $game->platforms()->attach($platforms);

            // Attach random tags (2-5 tags per game)
            $tags = \App\Models\Tag::inRandomOrder()
                ->limit(rand(2, 5))
                ->get();
            $game->tags()->attach($tags);
        }
    }
}
