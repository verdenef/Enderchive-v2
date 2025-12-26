<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = \App\Models\Game::all();

        if ($games->count() === 0) {
            return;
        }

        foreach ($games->random(min(30, $games->count())) as $game) {
            \App\Models\Review::factory(rand(3, 8))->create([
                'game_id' => $game->game_id,
            ]);
        }
    }
}


