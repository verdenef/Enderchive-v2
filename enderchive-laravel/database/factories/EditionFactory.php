<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Edition>
 */
class EditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $editionNames = [
            'Standard Edition', 'Deluxe Edition', 'Ultimate Edition', 'Collector\'s Edition',
            'Premium Edition', 'Gold Edition', 'Platinum Edition', 'Special Edition',
            'Limited Edition', 'Day One Edition', 'Pre-Order Edition', 'Game of the Year Edition',
            'Complete Edition', 'Definitive Edition', 'Remastered Edition'
        ];

        return [
            'game_id' => \App\Models\Game::factory(),
            'name' => $this->faker->randomElement($editionNames),
        ];
    }
}








