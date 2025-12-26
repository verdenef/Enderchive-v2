<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Edition;

class EditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = Game::all();
        $editionNames = [
            'Standard Edition', 'Deluxe Edition', 'Ultimate Edition', 'Collector\'s Edition',
            'Premium Edition', 'Gold Edition', 'Game of the Year Edition', 'Complete Edition'
        ];

        foreach ($games as $game) {
            // 70% chance of having at least one edition
            if (rand(1, 100) <= 70) {
                $numEditions = rand(1, 3);
                
                // Shuffle and take unique edition names to avoid duplicates
                shuffle($editionNames);
                $selectedEditions = array_slice($editionNames, 0, min($numEditions, count($editionNames)));
                
                foreach ($selectedEditions as $editionName) {
                    Edition::firstOrCreate([
                        'game_id' => $game->game_id,
                        'name' => $editionName,
                    ]);
                }
            }
        }
    }
}

