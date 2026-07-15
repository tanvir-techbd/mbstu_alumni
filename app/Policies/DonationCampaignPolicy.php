<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\DonationCampaign;
use App\Models\User;

class DonationCampaignPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }

    public function update(User $user, DonationCampaign $campaign): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }

    public function delete(User $user, DonationCampaign $campaign): bool
    {
        if (! $user->hasRole(RoleName::SuperAdmin->value)) {
            return false;
        }

        return $campaign->donations()->count() === 0;
    }

    public function viewReports(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }
}
