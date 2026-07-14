<?php

namespace App\Enums;

enum MentorshipStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
            self::Completed => 'Completed',
        };
    }
}
