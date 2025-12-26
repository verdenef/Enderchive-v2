<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Platform;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            'PC', 'PlayStation 5', 'Xbox Series X', 'Nintendo Switch', 'PlayStation 4',
            'Xbox One', 'Nintendo 3DS', 'PlayStation Vita', 'iOS', 'Android',
            'Steam Deck', 'PlayStation 3', 'Xbox 360', 'Wii U', 'Nintendo DS'
        ];

        foreach ($platforms as $name) {
            Platform::firstOrCreate(['name' => $name]);
        }
    }
}
