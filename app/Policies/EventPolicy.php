<?php

namespace App\Policies;

use App\Enums\EventStatus;
use App\Enums\RoleName;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function view(User $user, Event $event): bool
    {
        if ($event->status !== EventStatus::Draft) {
            return true;
        }

        return $this->manages($user, $event);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->hasRole(RoleName::Faculty->value);
    }

    public function update(User $user, Event $event): bool
    {
        return $this->manages($user, $event);
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->manages($user, $event);
    }

    public function publish(User $user, Event $event): bool
    {
        return $this->manages($user, $event);
    }

    public function archive(User $user, Event $event): bool
    {
        return $this->manages($user, $event);
    }

    public function manageParticipants(User $user, Event $event): bool
    {
        return $this->manages($user, $event);
    }

    public function cancelRegistration(User $user, Event $event): bool
    {
        return $event->isRegisteredBy($user);
    }

    private function manages(User $user, Event $event): bool
    {
        if ($user->hasRole(RoleName::SuperAdmin->value)) {
            return true;
        }

        return $user->hasRole(RoleName::Faculty->value) && $event->created_by === $user->id;
    }
}
