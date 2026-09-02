<?php

namespace App\Console\Commands;

use App\Enums\PermohonanStatus;
use App\Models\PermohonanInformasi;
use App\Services\PermohonanStatusService;
use App\Services\WorkingDayCalculator;
use Illuminate\Console\Command;

class CheckPermohonanDeadlines extends Command
{
    protected $signature = 'permohonan:check-deadlines';
    protected $description = 'Check and enforce SLA deadlines for permohonan informasi';

    public function __construct(
        private PermohonanStatusService $statusService,
        private WorkingDayCalculator $calculator,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking permohonan deadlines...');

        $this->autoCloseIncomplete();
        $this->warnApproachingDeadlines();
        $this->escalateOverdueAnswers();

        $this->info('Deadline check complete.');
        return self::SUCCESS;
    }

    /**
     * Auto-close permohonan that are past the 3 working day completeness deadline.
     */
    private function autoCloseIncomplete(): void
    {
        $overdue = PermohonanInformasi::where('status', PermohonanStatus::MenungguKelengkapan->value)
            ->whereNotNull('batas_waktu_lengkapi')
            ->where('batas_waktu_lengkapi', '<', now())
            ->get();

        foreach ($overdue as $permohonan) {
            try {
                $this->statusService->transition(
                    $permohonan,
                    PermohonanStatus::DitutupTidakLengkap,
                    null, // System actor
                    ['catatan' => 'Ditutup otomatis oleh sistem: pemohon tidak melengkapi berkas dalam batas waktu 3 hari kerja.'],
                );
                $this->line("  ✓ Closed: {$permohonan->nomor_registrasi}");
            } catch (\Exception $e) {
                $this->error("  ✗ Failed to close {$permohonan->nomor_registrasi}: {$e->getMessage()}");
            }
        }

        $this->info("  Auto-closed {$overdue->count()} incomplete permohonan.");
    }

    /**
     * Warn about permohonan approaching their answer deadline (1 working day left).
     */
    private function warnApproachingDeadlines(): void
    {
        $activeStatuses = [
            PermohonanStatus::Diverifikasi->value,
            PermohonanStatus::Ditugaskan->value,
            PermohonanStatus::MenungguData->value,
            PermohonanStatus::DataDiuji->value,
            PermohonanStatus::MenungguTandaTangan->value,
        ];

        $approaching = PermohonanInformasi::whereIn('status', $activeStatuses)
            ->whereNotNull('batas_waktu_jawaban')
            ->get()
            ->filter(function ($p) {
                $remaining = $this->calculator->remainingWorkingDays($p->batas_waktu_jawaban);
                return $remaining === 1; // Exactly 1 working day left
            });

        foreach ($approaching as $permohonan) {
            $this->warn("  ⚠ Approaching deadline: {$permohonan->nomor_registrasi} (1 hari kerja tersisa)");
            // TODO: Fire SLA warning notification to responsible role
        }

        $this->info("  Found {$approaching->count()} permohonan approaching deadline.");
    }

    /**
     * Escalate permohonan that have passed their answer deadline.
     */
    private function escalateOverdueAnswers(): void
    {
        $activeStatuses = [
            PermohonanStatus::Diverifikasi->value,
            PermohonanStatus::Ditugaskan->value,
            PermohonanStatus::MenungguData->value,
            PermohonanStatus::DataDiuji->value,
            PermohonanStatus::MenungguTandaTangan->value,
        ];

        $overdue = PermohonanInformasi::whereIn('status', $activeStatuses)
            ->whereNotNull('batas_waktu_jawaban')
            ->where('batas_waktu_jawaban', '<', now())
            ->get();

        foreach ($overdue as $permohonan) {
            $this->error("  🚨 OVERDUE: {$permohonan->nomor_registrasi} (melewati batas waktu jawaban)");
            // TODO: Fire escalation notification to Atasan PPID Pelaksana
        }

        $this->info("  Found {$overdue->count()} overdue permohonan.");
    }
}
