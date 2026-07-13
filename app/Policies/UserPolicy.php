<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }

    public function view(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }

    public function delete(User $user, User $target): bool
    {
        if (! $user->hasRole(RoleName::SuperAdmin->value)) {
            return false;
        }

        if ($user->is($target)) {
            return false;
        }

        if ($target->hasRole(RoleName::SuperAdmin->value) && User::role(RoleName::SuperAdmin->value)->count() <= 1) {
            return false;
        }

        return true;
    }

    public function toggleStatus(User $user, User $target): bool
    {
        return $this->delete($user, $target);
    }
}
