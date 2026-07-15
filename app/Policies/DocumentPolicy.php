<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->hasRole(RoleName::Faculty->value);
    }

    public function update(User $user, Document $document): bool
    {
        return $this->manages($user, $document);
    }

    public function delete(User $user, Document $document): bool
    {
        return $this->manages($user, $document);
    }

    private function manages(User $user, Document $document): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->id === $document->uploaded_by;
    }
}
