<?php

namespace Database\Factories;

use App\Enums\VerificationStatus;
use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AlumniProfile>
 */
class AlumniProfileFactory extends Factory
{
    public function definition(): array
    {
        $graduationYear = fake()->numberBetween(2005, 2023);

        return [
            'user_id' => User::factory(),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'date_of_birth' => fake()->dateTimeBetween('-45 years', '-22 years'),
            'student_id' => strtoupper(fake()->bothify('??-####')),
            'department' => fake()->randomElement(['CSE', 'EEE', 'ME', 'CE', 'BBA', 'English']),
            'program' => fake()->randomElement(['B.Sc.', 'B.A.', 'B.B.A.', 'M.Sc.']),
            'batch' => (string) fake()->numberBetween(1, 20),
            'session' => ($graduationYear - 4).'-'.$graduationYear,
            'graduation_year' => $graduationYear,
            'cgpa' => fake()->randomFloat(2, 2.5, 4.0),
            'company' => fake()->company(),
            'designation' => fake()->jobTitle(),
            'industry' => fake()->randomElement(['Software', 'Finance', 'Education', 'Telecom', 'Healthcare']),
            'years_of_experience' => fake()->numberBetween(0, 20),
            'country' => 'Bangladesh',
            'district' => fake()->randomElement(['Dhaka', 'Chattogram', 'Sylhet', 'Rajshahi', 'Khulna']),
            'office_address' => fake()->address(),
            'linkedin_url' => 'https://linkedin.com/in/'.fake()->userName(),
            'skills' => implode(', ', fake()->randomElements(['PHP', 'Laravel', 'JavaScript', 'Project Management', 'Data Analysis', 'UI/UX', 'DevOps'], 3)),
            'biography' => fake()->paragraph(),
            'interests' => implode(', ', fake()->randomElements(['Reading', 'Travel', 'Photography', 'Mentoring', 'Cricket'], 2)),
            'verification_status' => VerificationStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'verification_status' => VerificationStatus::Approved,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'verification_status' => VerificationStatus::Rejected,
            'rejection_reason' => 'Uploaded document is not legible. Please re-upload a clearer scan.',
            'reviewed_at' => now(),
        ]);
    }
}
