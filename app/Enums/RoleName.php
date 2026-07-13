<?php

namespace App\Enums;

enum RoleName: string
{
    case SuperAdmin = 'super-admin';
    case Alumni = 'alumni';
    case Student = 'student';
    case Faculty = 'faculty';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Alumni => 'Alumni',
            self::Student => 'Student',
            self::Faculty => 'Faculty',
        };
    }
}
