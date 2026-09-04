<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KeberatanController extends Controller
{
    protected $statusService;

    public function __construct(\App\Services\PermohonanStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    public function index(Request $request)
    {
        $query = \App\Models\PengajuanKeberatan::with('permohonan_informasi');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $keberatan = $query->orderBy('created_at', 'desc')->paginate(10);
        $currentStatus = $request->status;

        return view('admin.keberatan.index', compact('keberatan', 'currentStatus'));
    }

    public function show($id)
    {
        $keberatan = \App\Models\PengajuanKeberatan::with('permohonan_informasi.logs')->findOrFail($id);
        return view('admin.keberatan.show', compact('keberatan'));
    }

    public function store(Request $request, $permohonan_id)
    {
        $request->validate([
            'alasan_keberatan' => 'required|string',
            'keterangan_tambahan' => 'nullable|string'
        ]);

        $permohonan = \App\Models\PermohonanInformasi::findOrFail($permohonan_id);

        // Hanya bisa diajukan jika sudah mencapai state terminal ATAU batas waktu sudah lewat
        $isLate = false;
        if ($permohonan->batas_waktu_jawaban && \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($permohonan->batas_waktu_jawaban))) {
            $isLate = true;
        }

        if (!$permohonan->status->isTerminal() && !$isLate) {
            return back()->with('error', 'Permohonan ini belum memenuhi syarat untuk diajukan keberatan.');
        }

        $batasWaktuRespon = app(\App\Services\WorkingDayCalculator::class)->addWorkingDays(\Carbon\Carbon::now(), 30);

        $keberatan = \App\Models\PengajuanKeberatan::create([
            'permohonan_informasi_id' => $permohonan_id,
            'alasan_keberatan' => $request->alasan_keberatan,
            'keterangan_tambahan' => $request->keterangan_tambahan,
            'status' => \App\Enums\KeberatanStatus::Masuk,
            'batas_waktu_respon' => $batasWaktuRespon
        ]);

        $this->statusService->transition($permohonan, \App\Enums\PermohonanStatus::KeberatanDiajukan, auth()->user(), [
            'catatan' => 'Alasan: ' . $request->alasan_keberatan
        ]);

        return redirect()->route('admin.keberatan.index')->with('success', 'Pengajuan keberatan berhasil dibuat. Batas waktu respon: ' . $batasWaktuRespon->format('d M Y'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Diproses,Selesai,Ditolak',
            'tanggapan_atasan' => 'nullable|string'
        ]);

        $user = auth()->user();
        
        if (!$user->hasRole('Atasan PPID Pelaksana') && !$user->hasRole('Super Admin')) {
            abort(403, 'Hanya Atasan PPID Pelaksana yang berhak memproses sengketa.');
        }

        $keberatan = \App\Models\PengajuanKeberatan::findOrFail($id);
        
        $newStatus = \App\Enums\KeberatanStatus::tryFrom($request->status);
        if (!$newStatus) abort(400, 'Status tidak valid.');

        $keberatan->status = $newStatus;
        
        if ($request->has('tanggapan_atasan')) {
            $keberatan->tanggapan_atasan = $request->tanggapan_atasan;
        }

        if ($newStatus->isTerminal()) {
            $keberatan->tanggal_selesai = now();
            $keberatan->diputuskan_oleh = $user->id;
            $keberatan->diputuskan_at = now();
            
            // Sync Permohonan main status
            try {
                $this->statusService->transition(
                    $keberatan->permohonan_informasi,
                    \App\Enums\PermohonanStatus::KeberatanDiputuskan,
                    $user,
                    ['catatan' => 'Sengketa/Keberatan ditutup dengan status: ' . $newStatus->label()]
                );
            } catch (\Exception $e) {
                // Ignore if it's already in the terminal state
            }
        }

        $keberatan->save();

        \App\Models\PermohonanLog::create([
            'permohonan_informasi_id' => $keberatan->permohonan_informasi_id,
            'user_id' => $user->id,
            'aksi' => 'Update Status Keberatan: ' . $newStatus->label(),
            'catatan' => $request->tanggapan_atasan ?? '-'
        ]);

        return back()->with('success', "Status keberatan berhasil diperbarui.");
    }
}
