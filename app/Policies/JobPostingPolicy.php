<?php

namespace App\Policies;

use App\Enums\JobStatus;
use App\Enums\RoleName;
use App\Enums\VerificationStatus;
use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    public function view(User $user, JobPosting $job): bool
    {
        if ($job->status === JobStatus::Published) {
            return true;
        }

        return $this->manages($user, $job);
    }

    public function create(User $user): bool
    {
        if (! $user->hasRole(RoleName::Alumni->value)) {
            return false;
        }

        return $user->alumniProfile?->verification_status === VerificationStatus::Approved;
    }

    public function update(User $user, JobPosting $job): bool
    {
        return $this->manages($user, $job);
    }

    public function delete(User $user, JobPosting $job): bool
    {
        return $this->manages($user, $job);
    }

    public function review(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }

    private function manages(User $user, JobPosting $job): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->id === $job->posted_by;
    }
}
