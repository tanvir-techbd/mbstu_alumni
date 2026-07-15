<?php

namespace App\Services;

use App\Enums\CampaignStatus;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Models\User;

class DonationService
{
    public function createCampaign(array $data, User $creator): DonationCampaign
    {
        $campaign = new DonationCampaign($data);
        $campaign->created_by = $creator->id;
        $campaign->forceFill(['status' => CampaignStatus::Active]);
        $campaign->save();

        return $campaign;
    }

    public function updateCampaign(DonationCampaign $campaign, array $data): DonationCampaign
    {
        $campaign->fill($data);
        $campaign->save();

        return $campaign;
    }

    public function closeCampaign(DonationCampaign $campaign): void
    {
        $campaign->forceFill(['status' => CampaignStatus::Closed])->save();
    }

    public function reopenCampaign(DonationCampaign $campaign): void
    {
        $campaign->forceFill(['status' => CampaignStatus::Active])->save();
    }

    public function donate(DonationCampaign $campaign, User $donor, array $data): Donation
    {
        $donation = new Donation([
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'transaction_reference' => $data['transaction_reference'] ?? null,
            'donated_at' => now(),
        ]);
        $donation->donation_campaign_id = $campaign->id;
        $donation->user_id = $donor->id;
        $donation->receipt_number = 'PENDING';
        $donation->save();

        $donation->receipt_number = 'MBSTU-DON-'.str_pad((string) $donation->id, 6, '0', STR_PAD_LEFT);
        $donation->save();

        return $donation;
    }
}
