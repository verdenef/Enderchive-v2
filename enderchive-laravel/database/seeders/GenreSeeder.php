<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'Action', 'Adventure', 'RPG', 'Strategy', 'Simulation', 'Sports',
            'Racing', 'Fighting', 'Puzzle', 'Horror', 'Shooter', 'Platformer',
            'MMORPG', 'Sandbox', 'Survival', 'Stealth', 'Roguelike', 'Visual Novel',
            'Rhythm', 'Educational', 'Party', 'Card Game', 'Board Game'
        ];

        foreach ($genres as $name) {
            Genre::firstOrCreate(['name' => $name]);
        }
    }
}
