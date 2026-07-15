<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donation>
 */
class DonationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'donation_campaign_id' => DonationCampaign::factory(),
            'user_id' => User::factory(),
            'amount' => fake()->randomElement([100, 250, 500, 1000, 2000, 5000]),
            'payment_method' => fake()->randomElement(PaymentMethod::cases())->value,
            'transaction_reference' => fake()->optional(0.7)->bothify('TRX-########'),
            'receipt_number' => 'MBSTU-DON-'.fake()->unique()->numerify('######'),
            'donated_at' => now(),
        ];
    }
}
