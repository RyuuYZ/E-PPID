<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermohonanStatus;
use App\Enums\PenugasanStatus;
use App\Enums\HasilUji;
use App\Http\Controllers\Controller;
use App\Models\PenugasanPetugasPenghubung;
use App\Models\PermohonanInformasi;
use App\Models\UnitPengolah;
use App\Models\User;
use App\Services\PermohonanStatusService;
use Illuminate\Http\Request;

class PermohonanController extends Controller
{
    public function __construct(
        private PermohonanStatusService $statusService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PermohonanInformasi::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $permohonan = $query->orderBy('created_at', 'desc')->paginate(10);
        $currentStatus = $request->status;

        return view('admin.permohonan.index', compact('permohonan', 'currentStatus'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $permohonan = PermohonanInformasi::with(['penugasan.petugasPenghubung', 'penugasan.unitPengolah', 'logs'])->findOrFail($id);
        $unitPengolahs = UnitPengolah::all();
        $petugasPenghubungs = User::whereHas('role', function ($q) {
            $q->where('name', 'like', '%Petugas Penghubung%');
        })->get();

        return view('admin.permohonan.show', compact('permohonan', 'unitPengolahs', 'petugasPenghubungs'));
    }

    /**
     * Update the status of the specified resource via the state machine.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'target_status' => 'required|string',
        ]);

        $user = auth()->user();
        $permohonan = PermohonanInformasi::findOrFail($id);
        $targetStatus = PermohonanStatus::from($request->target_status);

        // Authorization checks per target status
        $this->authorizeTransition($user, $targetStatus);

        $metadata = ['catatan' => $request->catatan ?? null];

        // Specific metadata per transition
        if ($targetStatus === PermohonanStatus::MenungguKelengkapan) {
            $metadata['catatan'] = $request->alasan_tidak_lengkap ?? 'Berkas tidak lengkap.';
        }

        if ($targetStatus === PermohonanStatus::MenungguTandaTangan) {
            $metadata['surat_jawaban_path'] = $request->surat_jawaban_path ?? null;
        }

        try {
            $this->statusService->transition($permohonan, $targetStatus, $user, $metadata);
            return back()->with('success', "Status berhasil diperbarui menjadi: {$targetStatus->label()}");
        } catch (\App\Exceptions\InvalidStatusTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Assign permohonan to one or more petugas penghubung.
     */
    public function assignPetugas(Request $request, $id)
    {
        $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.petugas_penghubung_id' => 'required|exists:users,id',
            'assignments.*.unit_pengolah_id' => 'required|exists:unit_pengolahs,id',
            'assignments.*.instruksi' => 'nullable|string',
            'assignments.*.batas_waktu' => 'nullable|date',
        ]);

        $user = auth()->user();
        if (!$user->hasRole('PPID Pelaksana')) {
            abort(403, 'Hanya PPID Pelaksana yang dapat menugaskan petugas.');
        }

        $permohonan = PermohonanInformasi::findOrFail($id);

        // Transition to Ditugaskan if currently Diverifikasi
        if ($permohonan->status === PermohonanStatus::Diverifikasi) {
            $this->statusService->transition($permohonan, PermohonanStatus::Ditugaskan, $user, [
                'catatan' => 'Ditugaskan ke ' . count($request->assignments) . ' unit pengolah.',
            ]);
        }

        // Create penugasan records
        foreach ($request->assignments as $assignment) {
            PenugasanPetugasPenghubung::create([
                'permohonan_informasi_id' => $permohonan->id,
                'petugas_penghubung_id' => $assignment['petugas_penghubung_id'],
                'unit_pengolah_id' => $assignment['unit_pengolah_id'],
                'ditugaskan_oleh' => $user->id,
                'instruksi' => $assignment['instruksi'] ?? null,
                'batas_waktu' => $assignment['batas_waktu'] ?? null,
                'status' => PenugasanStatus::Ditugaskan,
                'hasil_uji' => HasilUji::Pending,
            ]);
        }

        // Transition to MenungguData
        $permohonan->refresh();
        if ($permohonan->status === PermohonanStatus::Ditugaskan) {
            $this->statusService->transition($permohonan, PermohonanStatus::MenungguData, $user, [
                'catatan' => 'Menunggu data dari petugas penghubung.',
            ]);
        }

        return back()->with('success', 'Penugasan berhasil dibuat.');
    }

    /**
     * Petugas Penghubung submits data for their assignment.
     */
    public function submitData(Request $request, $penugasanId)
    {
        $request->validate([
            'data_file' => 'required|file|max:10240',
            'catatan' => 'nullable|string',
        ]);

        $user = auth()->user();
        $penugasan = PenugasanPetugasPenghubung::findOrFail($penugasanId);

        // Ensure this petugas owns this assignment
        if ($penugasan->petugas_penghubung_id !== $user->id && !$user->hasRole('Super Admin')) {
            abort(403, 'Anda tidak memiliki akses ke penugasan ini.');
        }

        $path = $request->file('data_file')->store('penugasan_data', 'public');

        $penugasan->update([
            'data_path' => $path,
            'catatan_petugas_penghubung' => $request->catatan,
            'status' => PenugasanStatus::Diserahkan,
            'diserahkan_at' => now(),
        ]);

        // Check if all penugasan for this permohonan are now submitted
        $permohonan = $penugasan->permohonan;
        $allSubmitted = $permohonan->penugasan->every(
            fn($p) => $p->status === PenugasanStatus::Diserahkan
        );

        if ($allSubmitted && $permohonan->status === PermohonanStatus::MenungguData) {
            $this->statusService->transition($permohonan, PermohonanStatus::DataDiuji, null, [
                'catatan' => 'Semua data telah diserahkan oleh petugas penghubung.',
            ]);
        }

        return back()->with('success', 'Data berhasil diserahkan.');
    }

    /**
     * PPID Pelaksana reviews a penugasan submission.
     */
    public function reviewPenugasan(Request $request, $penugasanId)
    {
        $request->validate([
            'hasil_uji' => 'required|in:sesuai,perlu_revisi',
            'catatan_uji' => 'nullable|string',
        ]);

        $user = auth()->user();
        if (!$user->hasRole('PPID Pelaksana')) {
            abort(403, 'Hanya PPID Pelaksana yang dapat menguji data.');
        }

        $penugasan = PenugasanPetugasPenghubung::findOrFail($penugasanId);
        $penugasan->update([
            'hasil_uji' => HasilUji::from($request->hasil_uji),
            'catatan_uji' => $request->catatan_uji,
        ]);

        // If marked perlu_revisi, reset the penugasan status so petugas can resubmit
        if ($request->hasil_uji === 'perlu_revisi') {
            $penugasan->update([
                'status' => PenugasanStatus::Ditugaskan,
                'data_path' => null,
                'diserahkan_at' => null,
            ]);
        }

        return back()->with('success', 'Hasil pengujian berhasil disimpan.');
    }

    /**
     * Extend the answer deadline by 7 working days.
     */
    public function extendDeadline(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasRole('PPID Pelaksana')) {
            abort(403, 'Hanya PPID Pelaksana yang dapat memperpanjang batas waktu.');
        }

        $permohonan = PermohonanInformasi::findOrFail($id);

        try {
            $this->statusService->extendDeadline($permohonan, $user);
            return back()->with('success', 'Batas waktu jawaban berhasil diperpanjang (+7 hari kerja).');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Authorization guard based on target status.
     */
    private function authorizeTransition(User $user, PermohonanStatus $target): void
    {
        $allowed = match ($target) {
            PermohonanStatus::Diverifikasi,
            PermohonanStatus::MenungguKelengkapan,
            PermohonanStatus::Selesai => $user->hasRole('Desk Layanan'),

            PermohonanStatus::Ditugaskan,
            PermohonanStatus::MenungguTandaTangan => $user->hasRole('PPID Pelaksana'),

            PermohonanStatus::DataDiuji,
            PermohonanStatus::MenungguData => $user->hasRole('Petugas Penghubung') || $user->hasRole('PPID Pelaksana'),

            PermohonanStatus::Ditandatangani => $user->hasRole('Atasan PPID Pelaksana'),

            PermohonanStatus::DitutupTidakLengkap => true, // System or any authorized user

            default => $user->hasRole('Super Admin'),
        };

        if (!$allowed) {
            abort(403, "Anda tidak memiliki izin untuk memperbarui status ke: {$target->label()}");
        }
    }
}

