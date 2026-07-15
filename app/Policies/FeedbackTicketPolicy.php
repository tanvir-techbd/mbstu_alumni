<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\FeedbackTicket;
use App\Models\User;

class FeedbackTicketPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, FeedbackTicket $ticket): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value) || $user->id === $ticket->user_id;
    }

    public function reply(User $user, FeedbackTicket $ticket): bool
    {
        return $this->view($user, $ticket);
    }

    public function close(User $user, FeedbackTicket $ticket): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }
}
