<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserGameOwnership>
 */
class UserGameOwnershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ownershipTypes = ['Owned', 'Subscription', 'Borrowed', 'Gifted'];
        $mediaTypes = ['Digital', 'Physical'];

        return [
            'user_id' => \App\Models\User::factory(),
            'game_id' => \App\Models\Game::factory(),
            'platform_id' => \App\Models\Platform::factory(),
            'store_id' => function () {
                $stores = \App\Models\Store::all();
                if ($stores->isEmpty()) {
                    return null;
                }
                return $this->faker->optional(0.7)->randomElement($stores->pluck('store_id')->toArray());
            },
            'edition_id' => function (array $attributes) {
                $editions = \App\Models\Edition::where('game_id', $attributes['game_id'])->pluck('edition_id');
                if ($editions->isEmpty()) {
                    return null;
                }
                return $this->faker->optional(0.3)->randomElement($editions->toArray());
            },
            'ownership_type' => $this->faker->randomElement($ownershipTypes),
            'media_type' => $this->faker->randomElement($mediaTypes),
            'account_identifier' => $this->faker->optional(0.6)->userName(),
            'purchase_date' => $this->faker->optional(0.7)->dateTimeBetween('-2 years', 'now'),
            'purchase_price' => $this->faker->optional(0.6)->randomFloat(2, 5.99, 99.99),
            'purchase_currency' => $this->faker->optional(0.5)->randomElement(['USD', 'EUR', 'GBP', 'JPY']),
        ];
    }
}

