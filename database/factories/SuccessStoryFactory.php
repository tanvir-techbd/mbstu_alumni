<?php

namespace Database\Factories;

use App\Enums\SuccessStoryStatus;
use App\Models\SuccessStory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SuccessStory>
 */
class SuccessStoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => 'From MBSTU to '.fake()->company(),
            'story' => fake()->paragraphs(4, true),
            'company' => fake()->company(),
            'achievement' => fake()->randomElement([
                'Promoted to Senior Engineer',
                'Founded a startup',
                'Published research in an international journal',
                'Led a team of 20 engineers',
                'Won a national innovation award',
            ]),
            'status' => SuccessStoryStatus::Pending,
            'user_id' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => SuccessStoryStatus::Published]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => SuccessStoryStatus::Rejected,
            'rejection_reason' => 'Please add more specific details about your journey and achievements.',
        ]);
    }
}
