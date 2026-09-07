<?php

namespace App\Enums;

enum PermohonanStatus: string
{
    case Diajukan = 'diajukan';
    case Diverifikasi = 'diverifikasi';
    case MenungguKelengkapan = 'menunggu_kelengkapan';
    case DitutupTidakLengkap = 'ditutup_tidak_lengkap';
    case Ditugaskan = 'ditugaskan';
    case MenungguData = 'menunggu_data';
    case DataDiuji = 'data_diuji';
    case MenungguTandaTangan = 'menunggu_tanda_tangan';
    case Ditandatangani = 'ditandatangani';
    case Selesai = 'selesai';
    case KeberatanDiajukan = 'keberatan_diajukan';
    case KeberatanDiputuskan = 'keberatan_diputuskan';

    /**
     * Get the valid transitions FROM this status.
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Diajukan => [self::Diverifikasi, self::MenungguKelengkapan, self::KeberatanDiajukan],
            self::MenungguKelengkapan => [self::Diverifikasi, self::DitutupTidakLengkap],
            self::Diverifikasi => [self::Ditugaskan, self::KeberatanDiajukan],
            self::Ditugaskan => [self::MenungguData, self::KeberatanDiajukan],
            self::MenungguData => [self::DataDiuji, self::KeberatanDiajukan],
            self::DataDiuji => [self::MenungguTandaTangan, self::Ditugaskan, self::KeberatanDiajukan], // Ditugaskan = revisi
            self::MenungguTandaTangan => [self::Ditandatangani, self::KeberatanDiajukan],
            self::Ditandatangani => [self::Selesai, self::KeberatanDiajukan],
            self::Selesai => [self::KeberatanDiajukan],
            self::DitutupTidakLengkap => [self::KeberatanDiajukan],
            self::KeberatanDiajukan => [self::KeberatanDiputuskan],
            // Terminal state
            self::KeberatanDiputuskan => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions());
    }

    public function isTerminal(): bool
    {
        return empty($this->allowedTransitions());
    }

    /**
     * Human-readable label for UI display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Diajukan => 'Diajukan',
            self::Diverifikasi => 'Diverifikasi',
            self::MenungguKelengkapan => 'Menunggu Kelengkapan Berkas',
            self::DitutupTidakLengkap => 'Ditutup (Tidak Lengkap)',
            self::Ditugaskan => 'Ditugaskan ke Unit Pengolah',
            self::MenungguData => 'Menunggu Data dari Petugas',
            self::DataDiuji => 'Data Sedang Diuji',
            self::MenungguTandaTangan => 'Menunggu Tanda Tangan',
            self::Ditandatangani => 'Ditandatangani',
            self::Selesai => 'Selesai',
            self::KeberatanDiajukan => 'Keberatan Diajukan',
            self::KeberatanDiputuskan => 'Keberatan Diputuskan',
        };
    }

    /**
     * CSS classes for status badge rendering.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Diajukan => 'bg-blue-100 text-blue-700',
            self::Diverifikasi => 'bg-indigo-100 text-indigo-700',
            self::MenungguKelengkapan => 'bg-orange-100 text-orange-700',
            self::DitutupTidakLengkap => 'bg-red-100 text-red-700',
            self::Ditugaskan, self::MenungguData => 'bg-yellow-100 text-yellow-700',
            self::DataDiuji => 'bg-cyan-100 text-cyan-700',
            self::MenungguTandaTangan => 'bg-purple-100 text-purple-700',
            self::Ditandatangani => 'bg-emerald-100 text-emerald-700',
            self::Selesai => 'bg-green-100 text-green-700',
            self::KeberatanDiajukan => 'bg-rose-100 text-rose-700',
            self::KeberatanDiputuskan => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Which role is currently responsible for acting on this status.
     */
    public function responsibleRole(): ?string
    {
        return match ($this) {
            self::Diajukan, self::Ditandatangani => 'Desk Layanan',
            self::MenungguKelengkapan => null, // Waiting on Pemohon (public)
            self::Diverifikasi, self::DataDiuji => 'PPID Pelaksana',
            self::Ditugaskan, self::MenungguData => 'Petugas Penghubung',
            self::MenungguTandaTangan, self::KeberatanDiajukan => 'Atasan PPID Pelaksana',
            default => null, // Terminal or no specific role
        };
    }
}
