<?php

namespace Database\Factories;

use App\Enums\MentorshipStatus;
use App\Models\MentorshipRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MentorshipRequest>
 */
class MentorshipRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => User::factory(),
            'mentor_id' => User::factory(),
            'status' => MentorshipStatus::Pending,
            'message' => fake()->paragraph(),
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['status' => MentorshipStatus::Accepted]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => MentorshipStatus::Rejected,
            'rejection_reason' => 'I have too many mentees at the moment. Please try again next semester.',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => MentorshipStatus::Completed,
            'meeting_scheduled_at' => fake()->dateTimeBetween('-2 months', '-1 week'),
            'meeting_notes' => 'Video call via Google Meet.',
            'completed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
