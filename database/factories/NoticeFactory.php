<?php

namespace Database\Factories;

use App\Enums\NoticeType;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notice>
 */
class NoticeFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(NoticeType::cases());

        return [
            'title' => match ($type) {
                NoticeType::Scholarship => 'Merit Scholarship Applications Open for '.fake()->year(),
                NoticeType::Circular => 'Circular: '.fake()->sentence(4),
                NoticeType::News => 'MBSTU News: '.fake()->sentence(5),
                NoticeType::Announcement => 'Announcement: '.fake()->sentence(4),
                default => fake()->sentence(6),
            },
            'type' => $type->value,
            'content' => fake()->paragraphs(2, true),
            'posted_by' => User::factory(),
        ];
    }
}
