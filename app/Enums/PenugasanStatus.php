<?php

namespace App\Enums;

enum PenugasanStatus: string
{
    case Ditugaskan = 'ditugaskan';
    case Dikerjakan = 'dikerjakan';
    case Diserahkan = 'diserahkan';

    public function label(): string
    {
        return match ($this) {
            self::Ditugaskan => 'Ditugaskan',
            self::Dikerjakan => 'Sedang Dikerjakan',
            self::Diserahkan => 'Data Diserahkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Ditugaskan => 'bg-yellow-100 text-yellow-700',
            self::Dikerjakan => 'bg-blue-100 text-blue-700',
            self::Diserahkan => 'bg-green-100 text-green-700',
        };
    }
}
