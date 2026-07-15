<?php

namespace Database\Factories;

use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use App\Models\FeedbackTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeedbackTicket>
 */
class FeedbackTicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(FeedbackType::cases())->value,
            'subject' => fake()->sentence(6),
            'message' => fake()->paragraph(3),
            'status' => FeedbackStatus::Open->value,
            'closed_at' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => FeedbackStatus::Closed->value,
            'closed_at' => now()->subDays(fake()->numberBetween(1, 10)),
        ]);
    }
}
