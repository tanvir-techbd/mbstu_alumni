<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Gallery;
use App\Models\User;

class GalleryPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->hasRole(RoleName::Faculty->value);
    }

    public function update(User $user, Gallery $gallery): bool
    {
        return $this->manages($user, $gallery);
    }

    public function delete(User $user, Gallery $gallery): bool
    {
        return $this->manages($user, $gallery);
    }

    private function manages(User $user, Gallery $gallery): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->id === $gallery->created_by;
    }
}
