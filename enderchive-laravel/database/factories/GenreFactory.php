<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
 */
class GenreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genres = [
            'Action', 'Adventure', 'RPG', 'Strategy', 'Simulation', 'Sports',
            'Racing', 'Fighting', 'Puzzle', 'Horror', 'Shooter', 'Platformer',
            'MMORPG', 'Sandbox', 'Survival', 'Stealth', 'Roguelike', 'Visual Novel',
            'Rhythm', 'Educational', 'Party', 'Card Game', 'Board Game'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($genres),
        ];
    }
}
