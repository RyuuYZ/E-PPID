<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermohonanInformasi;
use Illuminate\Http\Request;

class PermohonanController extends Controller
{
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
        $permohonan = PermohonanInformasi::findOrFail($id);
        $unitPengolahs = \App\Models\UnitPengolah::all();
        
        return view('admin.permohonan.show', compact('permohonan', 'unitPengolahs'));
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'tahapan_proses' => 'required|string'
        ]);

        $user = auth()->user();
        $targetTahapan = $request->tahapan_proses;

        if (in_array($targetTahapan, ['Diverifikasi', 'Ditolak']) && !$user->hasRole('Desk Layanan')) {
            abort(403, 'Hanya Desk Layanan yang dapat memverifikasi atau menolak kelengkapan.');
        }
        if (in_array($targetTahapan, ['Ditugaskan', 'Menunggu TTE']) && !$user->hasRole('PPID Pelaksana')) {
            abort(403, 'Hanya PPID Pelaksana yang dapat melakukan aksi ini.');
        }
        if ($targetTahapan == 'Diuji' && !($user->hasRole('Petugas Penghubung') || $user->hasRole('PPID Pelaksana'))) {
            abort(403, 'Hanya Petugas Penghubung atau PPID Pelaksana yang dapat mengirimkan data.');
        }
        if ($targetTahapan == 'Selesai' && !$user->hasRole('Atasan PPID Pelaksana')) {
            abort(403, 'Hanya Atasan PPID Pelaksana yang dapat melakukan Tanda Tangan Elektronik.');
        }

        $permohonan = PermohonanInformasi::findOrFail($id);
        
        $oldTahapan = $permohonan->tahapan_proses;
        
        $permohonan->tahapan_proses = $request->tahapan_proses;
        
        if ($request->tahapan_proses == 'Ditugaskan' && $request->has('unit_pengolah_id')) {
            $permohonan->unit_pengolah_id = $request->unit_pengolah_id;
        }
        
        // Logika khusus berdasarkan pergantian status
        if ($request->tahapan_proses == 'Selesai') {
            $permohonan->tanggal_selesai = now();
            $permohonan->status = 'selesai'; 
            $aksiLog = 'Menandatangani & Menyelesaikan Permohonan';
            $catatanLog = 'Disetujui oleh Atasan PPID';
        } elseif ($request->tahapan_proses == 'Ditolak') {
            $permohonan->status = 'ditolak';
            $permohonan->tanggal_selesai = now();
            
            $alasan = $request->alasan_penolakan;
            if ($alasan === 'Lainnya') {
                $alasan = 'Lainnya: ' . $request->alasan_manual;
            } elseif ($request->alasan_manual) {
                $alasan .= "\nCatatan Tambahan: " . $request->alasan_manual;
            }
            $permohonan->keterangan_tidak_lengkap = $alasan;
            $aksiLog = 'Menolak Permohonan';
            $catatanLog = $alasan;
            
        } elseif ($request->tahapan_proses == 'Diverifikasi') {
            $permohonan->status = 'diproses';
            if (empty($permohonan->tanggal_jatuh_tempo)) {
                $permohonan->tanggal_jatuh_tempo = \Carbon\Carbon::now()->addWeekdays(10);
            }
            if ($request->has('catatan_verifikasi')) {
                $permohonan->keterangan_tidak_lengkap = $request->catatan_verifikasi;
            }
            $aksiLog = 'Memverifikasi Kelengkapan Berkas';
            $catatanLog = $request->catatan_verifikasi ?? 'Berkas dinyatakan lengkap.';
        } elseif ($request->tahapan_proses == 'Ditutup') {
            $permohonan->status = 'ditutup';
            $aksiLog = 'Menutup Permohonan';
            $catatanLog = 'Permohonan ditutup oleh sistem/admin.';
        } elseif ($request->tahapan_proses == 'Ditugaskan') {
            $permohonan->status = 'diproses';
            if ($oldTahapan == 'Diuji') {
                $aksiLog = 'Mengembalikan ke Petugas Penghubung (Revisi)';
                $catatanLog = $request->catatan_revisi ?? 'Mohon perbaiki data yang dikirim.';
            } else {
                $aksiLog = 'Mendisposisikan ke Unit Pengolah';
                $unit = \App\Models\UnitPengolah::find($request->unit_pengolah_id);
                $catatanLog = 'Ditugaskan ke: ' . ($unit ? $unit->nama_bidang : 'Unit Terkait');
            }
        } elseif ($request->tahapan_proses == 'Diuji') {
            $permohonan->status = 'diproses';
            $aksiLog = 'Menyerahkan Data untuk Diuji';
            $catatanLog = 'Data diserahkan oleh Petugas Penghubung.';
        } elseif ($request->tahapan_proses == 'Menunggu TTE') {
            $permohonan->status = 'diproses';
            $aksiLog = 'Mengajukan Draf Jawaban ke Atasan';
            $catatanLog = $request->catatan ?? 'Telah divalidasi oleh PPID Pelaksana.';
        } else {
            $permohonan->status = 'diproses';
            $aksiLog = 'Memperbarui Status: ' . $request->tahapan_proses;
            $catatanLog = '';
        }

        $permohonan->save();

        // Mencatat Log Aktivitas
        \App\Models\PermohonanLog::create([
            'permohonan_informasi_id' => $permohonan->id,
            'user_id' => $user->id,
            'tahapan_proses' => $request->tahapan_proses,
            'aksi' => $aksiLog,
            'catatan' => $catatanLog
        ]);

        return back()->with('success', "Tahapan permohonan berhasil diperbarui menjadi: " . $request->tahapan_proses);
    }
}
