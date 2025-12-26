<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Platform>
 */
class PlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $platforms = [
            'PC', 'PlayStation 5', 'Xbox Series X', 'Nintendo Switch', 'PlayStation 4',
            'Xbox One', 'Nintendo 3DS', 'PlayStation Vita', 'iOS', 'Android',
            'Steam Deck', 'PlayStation 3', 'Xbox 360', 'Wii U', 'Nintendo DS'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($platforms),
        ];
    }
}
