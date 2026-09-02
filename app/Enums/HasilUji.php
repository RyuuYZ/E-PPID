<?php

namespace App\Enums;

enum HasilUji: string
{
    case Pending = 'pending';
    case Sesuai = 'sesuai';
    case PerluRevisi = 'perlu_revisi';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Pengujian',
            self::Sesuai => 'Data Sesuai',
            self::PerluRevisi => 'Perlu Revisi',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-gray-100 text-gray-600',
            self::Sesuai => 'bg-green-100 text-green-700',
            self::PerluRevisi => 'bg-red-100 text-red-700',
        };
    }
}
