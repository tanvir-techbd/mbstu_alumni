<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Donation;
use App\Models\User;

class DonationPolicy
{
    public function view(User $user, Donation $donation): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->id === $donation->user_id;
    }
}
