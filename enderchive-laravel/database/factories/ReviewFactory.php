<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate dynamic review content using Faker
        $reviewText = $this->faker->paragraphs(rand(1, 3), true);
        
        // Sometimes add gaming-specific phrases
        $gamingPhrases = [
            'The gameplay mechanics are ' . $this->faker->randomElement(['solid', 'innovative', 'refined', 'challenging', 'smooth']),
            'Graphics are ' . $this->faker->randomElement(['stunning', 'beautiful', 'impressive', 'detailed', 'realistic']),
            'The story is ' . $this->faker->randomElement(['engaging', 'compelling', 'emotional', 'well-written', 'captivating']),
            'Multiplayer experience is ' . $this->faker->randomElement(['fun', 'competitive', 'balanced', 'exciting', 'addictive']),
            'Single player mode is ' . $this->faker->randomElement(['excellent', 'immersive', 'challenging', 'rewarding', 'satisfying']),
            'Controls feel ' . $this->faker->randomElement(['responsive', 'tight', 'intuitive', 'precise', 'fluid']),
            'The difficulty is ' . $this->faker->randomElement(['perfect', 'challenging', 'accessible', 'balanced', 'scaled well']),
            'Replay value is ' . $this->faker->randomElement(['high', 'excellent', 'good', 'limited', 'infinite']),
            'Performance is ' . $this->faker->randomElement(['smooth', 'stable', 'optimized', 'inconsistent', 'poor']),
            'Content is ' . $this->faker->randomElement(['abundant', 'sufficient', 'limited', 'varied', 'repetitive'])
        ];

        // Combine faker paragraph with gaming phrases
        $finalReview = $this->faker->randomElement($gamingPhrases) . '. ' . $reviewText;

        return [
            'user_id' => \App\Models\User::factory(),
            'game_id' => \App\Models\Game::factory(),
            'rating' => $this->faker->numberBetween(1, 10),
            'review_text' => $finalReview,
        ];
    }
}
