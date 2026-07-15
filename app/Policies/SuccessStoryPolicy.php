<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Enums\SuccessStoryStatus;
use App\Enums\VerificationStatus;
use App\Models\SuccessStory;
use App\Models\User;

class SuccessStoryPolicy
{
    public function view(User $user, SuccessStory $story): bool
    {
        if ($story->status === SuccessStoryStatus::Published) {
            return true;
        }

        return $this->manages($user, $story);
    }

    public function create(User $user): bool
    {
        if (! $user->hasRole(RoleName::Alumni->value)) {
            return false;
        }

        return $user->alumniProfile?->verification_status === VerificationStatus::Approved;
    }

    public function update(User $user, SuccessStory $story): bool
    {
        return $this->manages($user, $story);
    }

    public function delete(User $user, SuccessStory $story): bool
    {
        return $this->manages($user, $story);
    }

    public function review(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }

    private function manages(User $user, SuccessStory $story): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->id === $story->user_id;
    }
}
