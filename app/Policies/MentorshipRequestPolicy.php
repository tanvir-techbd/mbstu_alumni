<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Enums\VerificationStatus;
use App\Models\MentorshipRequest;
use App\Models\User;

class MentorshipRequestPolicy
{
    public function view(User $user, MentorshipRequest $request): bool
    {
        return $user->id === $request->student_id
            || $user->id === $request->mentor_id
            || $user->hasRole(RoleName::SuperAdmin->value);
    }

    public function request(User $user, User $mentor): bool
    {
        if (! $user->hasRole(RoleName::Student->value)) {
            return false;
        }

        if (! $mentor->hasRole(RoleName::Alumni->value)) {
            return false;
        }

        return $mentor->alumniProfile?->verification_status === VerificationStatus::Approved;
    }

    public function respond(User $user, MentorshipRequest $request): bool
    {
        return $user->id === $request->mentor_id;
    }

    public function withdraw(User $user, MentorshipRequest $request): bool
    {
        return $user->id === $request->student_id;
    }
}
