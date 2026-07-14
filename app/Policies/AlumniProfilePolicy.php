<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\AlumniProfile;
use App\Models\User;

class AlumniProfilePolicy
{
    public function view(User $user, AlumniProfile $profile): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->is($profile->user);
    }

    public function update(User $user, AlumniProfile $profile): bool
    {
        return $user->is($profile->user);
    }

    public function review(User $user, AlumniProfile $profile): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }

    public function viewAnyForReview(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }
}
