<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $eventDate = fake()->dateTimeBetween('+1 week', '+3 months');

        return [
            'title' => fake()->randomElement(['Annual Alumni Reunion', 'Career Fair', 'Tech Talk Series', 'Convocation Ceremony', 'Alumni Networking Night', 'Homecoming Weekend']).' '.fake()->year(),
            'description' => fake()->paragraphs(3, true),
            'venue' => fake()->randomElement(['MBSTU Auditorium', 'Central Playground', 'CSE Building Seminar Hall', 'Administrative Building Conference Room']),
            'event_date' => $eventDate,
            'event_time' => fake()->time('H:i:s'),
            'registration_deadline' => (clone $eventDate)->modify('-3 days'),
            'capacity' => fake()->optional(0.6)->numberBetween(30, 200),
            'status' => EventStatus::Draft,
            'created_by' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => EventStatus::Published]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => EventStatus::Archived,
            'event_date' => fake()->dateTimeBetween('-6 months', '-1 week'),
            'registration_deadline' => fake()->dateTimeBetween('-7 months', '-2 weeks'),
        ]);
    }
}
