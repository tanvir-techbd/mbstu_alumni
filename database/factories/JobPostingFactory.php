<?php

namespace Database\Factories;

use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobPosting>
 */
class JobPostingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company' => fake()->company(),
            'position' => fake()->jobTitle(),
            'category' => fake()->randomElement(['Engineering', 'Marketing', 'Finance', 'Design', 'Operations', 'Sales']),
            'employment_type' => fake()->randomElement(EmploymentType::cases())->value,
            'salary' => fake()->optional(0.7)->randomElement(['Negotiable', '30,000 - 45,000 BDT', '50,000 - 70,000 BDT', '$60,000 - $80,000/yr']),
            'experience' => fake()->optional(0.8)->randomElement(['Entry level', '1-2 years', '2-4 years', '5+ years']),
            'location' => fake()->randomElement(['Dhaka, Bangladesh', 'Chattogram, Bangladesh', 'Remote', 'Sylhet, Bangladesh']),
            'deadline' => fake()->dateTimeBetween('+1 week', '+2 months'),
            'description' => fake()->paragraphs(3, true),
            'apply_url' => 'https://'.fake()->domainName().'/careers/apply',
            'status' => JobStatus::Pending,
            'posted_by' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => JobStatus::Published]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => JobStatus::Rejected,
            'rejection_reason' => 'Apply URL does not point to a valid careers page. Please correct and resubmit.',
        ]);
    }
}
