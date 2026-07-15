<?php

namespace App\Enums;

enum GalleryCategory: string
{
    case Reunion = 'reunion';
    case Convocation = 'convocation';
    case Seminar = 'seminar';
    case Workshop = 'workshop';
    case CulturalProgram = 'cultural-program';

    public function label(): string
    {
        return match ($this) {
            self::Reunion => 'Reunion',
            self::Convocation => 'Convocation',
            self::Seminar => 'Seminar',
            self::Workshop => 'Workshop',
            self::CulturalProgram => 'Cultural Program',
        };
    }
}
