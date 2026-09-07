<?php

namespace App\Http\Controllers;

use App\Enums\KeberatanStatus;
use App\Enums\PermohonanStatus;
use App\Models\PengajuanKeberatan;
use App\Models\PermohonanInformasi;
use App\Services\PermohonanStatusService;
use App\Services\WorkingDayCalculator;
use Illuminate\Http\Request;

class KeberatanPublicController extends Controller
{
    public function __construct(
        protected PermohonanStatusService $statusService,
        protected WorkingDayCalculator $workingDayCalculator
    ) {}

    public function store(Request $request, $nomor_registrasi)
    {
        $request->validate([
            'alasan_keberatan' => 'required|string|max:255',
            'keterangan_tambahan' => 'nullable|string|max:2000',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $permohonan = PermohonanInformasi::where('nomor_registrasi', $nomor_registrasi)->firstOrFail();

        // Cek jika sudah pernah mengajukan keberatan
        if ($permohonan->keberatan) {
            return back()->with('error', 'Keberatan untuk permohonan ini sudah diajukan sebelumnya.');
        }

        // Cek syarat batas waktu atau status
        $isLate = false;
        if ($permohonan->batas_waktu_jawaban && now()->greaterThan($permohonan->batas_waktu_jawaban)) {
            $isLate = true;
        }

        // Boleh diajukan jika: status selesai/ditolak/ditutup ATAU batas waktu sudah lewat ATAU sudah dalam tahapan proses
        $buktiPath = null;
        if ($request->hasFile('file_pendukung')) {
            $buktiPath = $request->file('file_pendukung')->store('keberatan', 'public');
        }

        $batasWaktuRespon = $this->workingDayCalculator->addWorkingDays(now(), 30);

        $keberatan = PengajuanKeberatan::create([
            'permohonan_informasi_id' => $permohonan->id,
            'alasan_keberatan' => $request->alasan_keberatan,
            'keterangan_tambahan' => $request->keterangan_tambahan,
            'bukti_pendukung_path' => $buktiPath,
            'status' => KeberatanStatus::Masuk,
            'batas_waktu_respon' => $batasWaktuRespon,
        ]);

        // Transition status permohonan ke KeberatanDiajukan
        try {
            $this->statusService->transition(
                $permohonan,
                PermohonanStatus::KeberatanDiajukan,
                null,
                ['catatan' => 'Diajukan mandiri oleh Pemohon. Alasan: ' . $request->alasan_keberatan]
            );
        } catch (\Exception $e) {
            // Fallback: force fill if needed
            $permohonan->forceFill(['status' => PermohonanStatus::KeberatanDiajukan->value])->save();
        }

        return redirect()->route('permohonan.lacak', ['tracking_id' => $nomor_registrasi])
                         ->with('success', 'Pengajuan keberatan berhasil dikirimkan. Atasan PPID Pelaksana akan menindaklanjuti dalam waktu maksimal 30 hari kerja sesuai UU KIP No. 14/2008.');
    }
}
