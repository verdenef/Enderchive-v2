<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Friend>
 */
class FriendFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['Pending', 'Accepted', 'Blocked'];

        return [
            'user_id' => \App\Models\User::factory(),
            'friend_user_id' => \App\Models\User::factory(),
            'status' => $this->faker->randomElement($statuses),
        ];
    }
}
