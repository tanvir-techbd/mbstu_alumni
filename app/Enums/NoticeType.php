<?php

namespace App\Enums;

enum NoticeType: string
{
    case Notice = 'notice';
    case Circular = 'circular';
    case Scholarship = 'scholarship';
    case News = 'news';
    case Announcement = 'announcement';

    public function label(): string
    {
        return match ($this) {
            self::Notice => 'Notice',
            self::Circular => 'Circular',
            self::Scholarship => 'Scholarship',
            self::News => 'News',
            self::Announcement => 'Announcement',
        };
    }
}
