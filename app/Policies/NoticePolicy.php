<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Notice;
use App\Models\User;

class NoticePolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->hasRole(RoleName::Faculty->value);
    }

    public function update(User $user, Notice $notice): bool
    {
        return $this->manages($user, $notice);
    }

    public function delete(User $user, Notice $notice): bool
    {
        return $this->manages($user, $notice);
    }

    private function manages(User $user, Notice $notice): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->id === $notice->posted_by;
    }
}
