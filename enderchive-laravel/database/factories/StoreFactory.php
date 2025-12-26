<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stores = [
            'Steam', 'Epic Games Store', 'GOG', 'PlayStation Store', 'Xbox Store',
            'Nintendo eShop', 'Origin', 'Uplay', 'Battle.net', 'Itch.io',
            'Humble Store', 'Green Man Gaming', 'Fanatical', 'Gamesplanet',
            'GameStop', 'Amazon Games', 'Best Buy', 'Target', 'Walmart'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($stores),
        ];
    }
}








