<?php

namespace App\Enums;

enum KeberatanStatus: string
{
    case Masuk = 'Masuk';
    case Diproses = 'Diproses';
    case Selesai = 'Selesai';
    case Ditolak = 'Ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Masuk => 'Masuk',
            self::Diproses => 'Diproses',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Masuk => 'bg-blue-50 text-blue-600 border-blue-200',
            self::Diproses => 'bg-yellow-50 text-yellow-600 border-yellow-200',
            self::Selesai => 'bg-green-50 text-green-600 border-green-200',
            self::Ditolak => 'bg-red-50 text-red-600 border-red-200',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Selesai, self::Ditolak]);
    }
}
