<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Multiplayer', 'Single Player', 'Co-op', 'Online', 'Offline',
            'Story Rich', 'Open World', 'First Person', 'Third Person',
            'Indie', 'AAA', 'Early Access', 'Free to Play', 'VR',
            'Controller Support', 'Keyboard & Mouse', 'Touch Controls',
            'Casual', 'Hardcore', 'Competitive', 'Relaxing', 'Challenging',
            'Fantasy', 'Sci-Fi', 'Horror', 'Comedy', 'Drama', 'Romance',
            'Retro', 'Modern', 'Futuristic', 'Medieval', 'Post-Apocalyptic',
            '2D', '3D', 'Pixel Art', 'Realistic', 'Cartoon', 'Anime'
        ];

        foreach ($tags as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }
    }
}
