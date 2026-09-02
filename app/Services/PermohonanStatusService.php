<?php

namespace App\Services;

use App\Enums\PermohonanStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\PermohonanInformasi;
use App\Models\PermohonanLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PermohonanStatusService
{
    public function __construct(
        private WorkingDayCalculator $workingDayCalculator,
    ) {}

    /**
     * Check if a transition is valid.
     */
    public function canTransitionTo(PermohonanInformasi $permohonan, PermohonanStatus $to): bool
    {
        return $permohonan->status->canTransitionTo($to);
    }

    /**
     * Perform a status transition. This is the ONLY allowed way to change permohonan status.
     *
     * @param PermohonanInformasi $permohonan
     * @param PermohonanStatus $newStatus
     * @param User|null $actor  The user performing the action (null = system)
     * @param array $metadata   Extra data (catatan, unit_pengolah_id, etc.)
     *
     * @throws InvalidStatusTransitionException
     */
    public function transition(
        PermohonanInformasi $permohonan,
        PermohonanStatus $newStatus,
        ?User $actor = null,
        array $metadata = [],
    ): void {
        $oldStatus = $permohonan->status;

        if (!$oldStatus->canTransitionTo($newStatus)) {
            throw new InvalidStatusTransitionException($oldStatus, $newStatus);
        }

        DB::transaction(function () use ($permohonan, $oldStatus, $newStatus, $actor, $metadata) {
            // Apply side effects based on the transition
            $this->applySideEffects($permohonan, $oldStatus, $newStatus, $actor, $metadata);

            // Persist the new status (bypass mutator by using query builder on the model)
            $permohonan->forceFill(['status' => $newStatus->value])->save();

            // Log the transition
            $this->logTransition($permohonan, $oldStatus, $newStatus, $actor, $metadata);
        });
    }

    /**
     * Apply business-rule side effects for each transition.
     */
    private function applySideEffects(
        PermohonanInformasi $permohonan,
        PermohonanStatus $from,
        PermohonanStatus $to,
        ?User $actor,
        array $metadata,
    ): void {
        $now = now();

        match ($to) {
            PermohonanStatus::Diverifikasi => $this->onVerified($permohonan, $actor),
            PermohonanStatus::MenungguKelengkapan => $this->onIncomplete($permohonan, $metadata),
            PermohonanStatus::DitutupTidakLengkap => $permohonan->forceFill(['ditutup_at' => $now]),
            PermohonanStatus::Ditugaskan => $this->onAssigned($permohonan, $actor, $metadata),
            PermohonanStatus::MenungguData => null, // Side effects handled in penugasan
            PermohonanStatus::DataDiuji => null,
            PermohonanStatus::MenungguTandaTangan => $permohonan->forceFill([
                'surat_jawaban_path' => $metadata['surat_jawaban_path'] ?? $permohonan->surat_jawaban_path,
            ]),
            PermohonanStatus::Ditandatangani => $permohonan->forceFill([
                'ditandatangani_oleh' => $actor?->id,
                'ditandatangani_at' => $now,
            ]),
            PermohonanStatus::Selesai => $permohonan->forceFill([
                'dikirim_at' => $now,
                'tanggal_selesai' => $now,
            ]),
            PermohonanStatus::KeberatanDiajukan => null, // Handled in KeberatanController
            PermohonanStatus::KeberatanDiputuskan => null,
        };
    }

    private function onVerified(PermohonanInformasi $permohonan, ?User $actor): void
    {
        $permohonan->forceFill([
            'desk_layanan_id' => $actor?->id,
            'batas_waktu_jawaban' => $this->workingDayCalculator->addWorkingDays(now(), 10),
            'tanggal_jatuh_tempo' => $this->workingDayCalculator->addWorkingDays(now(), 10),
        ]);
    }

    private function onIncomplete(PermohonanInformasi $permohonan, array $metadata): void
    {
        $permohonan->forceFill([
            'keterangan_tidak_lengkap' => $metadata['catatan'] ?? null,
            'batas_waktu_lengkapi' => $this->workingDayCalculator->addWorkingDays(now(), 3),
        ]);
    }

    private function onAssigned(PermohonanInformasi $permohonan, ?User $actor, array $metadata): void
    {
        $permohonan->forceFill([
            'ppid_pelaksana_id' => $actor?->id,
        ]);
    }

    /**
     * Write a log entry for the transition.
     */
    private function logTransition(
        PermohonanInformasi $permohonan,
        PermohonanStatus $from,
        PermohonanStatus $to,
        ?User $actor,
        array $metadata,
    ): void {
        $aksi = $this->describeTransition($from, $to);
        $catatan = $metadata['catatan'] ?? $aksi;

        PermohonanLog::create([
            'permohonan_informasi_id' => $permohonan->id,
            'user_id' => $actor?->id,
            'tahapan_proses' => $to->value,
            'aksi' => $aksi,
            'catatan' => $catatan,
        ]);
    }

    private function describeTransition(PermohonanStatus $from, PermohonanStatus $to): string
    {
        return match ($to) {
            PermohonanStatus::Diverifikasi => 'Memverifikasi Kelengkapan Berkas',
            PermohonanStatus::MenungguKelengkapan => 'Menandai Berkas Tidak Lengkap',
            PermohonanStatus::DitutupTidakLengkap => 'Menutup Permohonan (Berkas Tidak Dilengkapi)',
            PermohonanStatus::Ditugaskan => $from === PermohonanStatus::DataDiuji
                ? 'Mengembalikan ke Petugas Penghubung (Revisi)'
                : 'Mendisposisikan ke Unit Pengolah',
            PermohonanStatus::MenungguData => 'Menunggu Data dari Petugas Penghubung',
            PermohonanStatus::DataDiuji => 'Data Diserahkan untuk Diuji',
            PermohonanStatus::MenungguTandaTangan => 'Mengajukan Draf Jawaban ke Atasan',
            PermohonanStatus::Ditandatangani => 'Menandatangani Surat Jawaban',
            PermohonanStatus::Selesai => 'Mengirimkan Jawaban ke Pemohon',
            PermohonanStatus::KeberatanDiajukan => 'Mengajukan Keberatan',
            PermohonanStatus::KeberatanDiputuskan => 'Memutuskan Keberatan',
            default => "Transisi: {$from->label()} → {$to->label()}",
        };
    }

    /**
     * Extend the answer deadline by 7 working days (can only be done once).
     */
    public function extendDeadline(PermohonanInformasi $permohonan, ?User $actor = null): void
    {
        if ($permohonan->diperpanjang) {
            throw new \RuntimeException('Batas waktu sudah pernah diperpanjang sebelumnya.');
        }

        $currentDeadline = $permohonan->batas_waktu_jawaban ?? now();
        $newDeadline = $this->workingDayCalculator->addWorkingDays($currentDeadline, 7);

        $permohonan->forceFill([
            'diperpanjang' => true,
            'batas_waktu_jawaban' => $newDeadline,
            'tanggal_jatuh_tempo' => $newDeadline,
        ])->save();

        PermohonanLog::create([
            'permohonan_informasi_id' => $permohonan->id,
            'user_id' => $actor?->id,
            'tahapan_proses' => $permohonan->status->value,
            'aksi' => 'Memperpanjang Batas Waktu Jawaban (+7 Hari Kerja)',
            'catatan' => 'Batas waktu baru: ' . $newDeadline->format('d M Y'),
        ]);
    }
}
