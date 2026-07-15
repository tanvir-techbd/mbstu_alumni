<?php

namespace Database\Factories;

use App\Enums\CampaignStatus;
use App\Models\DonationCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DonationCampaign>
 */
class DonationCampaignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Scholarship Fund for Underprivileged Students',
                'New Library Building Campaign',
                'CSE Lab Equipment Upgrade',
                'Emergency Student Relief Fund',
                'Annual Alumni Endowment',
            ]),
            'description' => fake()->paragraphs(2, true),
            'goal_amount' => fake()->randomElement([50000, 100000, 250000, 500000]),
            'start_date' => now()->subMonths(2),
            'end_date' => fake()->optional(0.5)->dateTimeBetween('+1 month', '+6 months'),
            'status' => CampaignStatus::Active,
            'created_by' => User::factory(),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => ['status' => CampaignStatus::Closed]);
    }
}
