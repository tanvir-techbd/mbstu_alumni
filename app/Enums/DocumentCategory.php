<?php

namespace App\Enums;

enum DocumentCategory: string
{
    case Newsletter = 'newsletter';
    case AnnualReport = 'annual-report';
    case Magazine = 'magazine';
    case Forms = 'forms';

    public function label(): string
    {
        return match ($this) {
            self::Newsletter => 'Newsletter',
            self::AnnualReport => 'Annual Report',
            self::Magazine => 'Magazine',
            self::Forms => 'Forms',
        };
    }
}
