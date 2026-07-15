<?php

namespace App\Enums;

enum SuccessStoryStatus: string
{
    case Pending = 'pending';
    case Published = 'published';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Published => 'Published',
            self::Rejected => 'Rejected',
        };
    }
}
