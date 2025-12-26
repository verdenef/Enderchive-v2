<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
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

        return [
            'name' => $this->faker->unique()->randomElement($tags),
        ];
    }
}
