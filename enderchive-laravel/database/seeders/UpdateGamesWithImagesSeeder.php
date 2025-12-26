<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

class UpdateGamesWithImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update all existing games that don't have cover_image
        $games = Game::whereNull('cover_image')->orWhere('cover_image', '')->get();
        
        foreach ($games as $game) {
            // Generate a placeholder image URL for each game
            // Using Lorem Picsum with game_id as seed for consistent but unique images
            $game->cover_image = 'https://picsum.photos/seed/' . $game->game_id . '/300/400';
            $game->save();
        }
        
        $this->command->info("Updated {$games->count()} games with cover images.");
    }
}

