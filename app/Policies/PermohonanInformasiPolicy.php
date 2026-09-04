<?php

namespace App\Policies;

use App\Models\PermohonanInformasi;
use App\Models\User;
use App\Enums\PermohonanStatus;

class PermohonanInformasiPolicy
{
    /**
     * Bypass all policy checks for Super Admin
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All internal roles can view the index (filtered by controller)
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PermohonanInformasi $permohonan): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Petugas Penghubung can only view if it has been assigned to them
        if ($user->hasRole('Petugas Penghubung')) {
            return $permohonan->penugasan()->where('petugas_penghubung_id', $user->id)->exists();
        }

        // Other roles (Desk Layanan, PPID Pelaksana, Atasan) can view all
        return true;
    }

    /**
     * Determine whether the user can view sensitive data (NIK, alamat, file identitas).
     * Sesuai requirement: Petugas Penghubung TIDAK boleh melihat NIK.
     */
    public function viewSensitiveData(User $user, PermohonanInformasi $permohonan): bool
    {
        if ($user->hasRole('Petugas Penghubung')) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can verify the permohonan.
     */
    public function verify(User $user, PermohonanInformasi $permohonan): bool
    {
        if (!$user->hasRole('Desk Layanan')) {
            return false;
        }

        return $permohonan->status === PermohonanStatus::Diajukan || 
               $permohonan->status === PermohonanStatus::MenungguKelengkapan;
    }

    /**
     * Determine whether the user can assign to Petugas Penghubung.
     */
    public function assign(User $user, PermohonanInformasi $permohonan): bool
    {
        if (!$user->hasRole('PPID Pelaksana')) {
            return false;
        }

        return in_array($permohonan->status, [
            PermohonanStatus::Diverifikasi,
            PermohonanStatus::DataDiuji // Can re-assign for revision
        ]);
    }

    /**
     * Determine whether the user can upload draft answer to Atasan.
     */
    public function draftAnswer(User $user, PermohonanInformasi $permohonan): bool
    {
        if (!$user->hasRole('PPID Pelaksana')) {
            return false;
        }

        return $permohonan->status === PermohonanStatus::DataDiuji && $permohonan->allPenugasanSesuai();
    }

    /**
     * Determine whether the user can sign the final answer.
     */
    public function sign(User $user, PermohonanInformasi $permohonan): bool
    {
        if (!$user->hasRole('Atasan PPID Pelaksana')) {
            return false;
        }

        return $permohonan->status === PermohonanStatus::MenungguTandaTangan;
    }

    /**
     * Determine whether the user can send the final answer to Pemohon.
     */
    public function sendAnswer(User $user, PermohonanInformasi $permohonan): bool
    {
        if (!$user->hasRole('Desk Layanan')) {
            return false;
        }

        return $permohonan->status === PermohonanStatus::Ditandatangani;
    }
}
