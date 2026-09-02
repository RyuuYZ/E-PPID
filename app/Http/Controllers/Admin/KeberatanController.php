<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KeberatanController extends Controller
{
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

        // Hanya bisa diajukan jika sudah selesai, ditolak, atau ditutup
        if (!in_array($permohonan->status, ['selesai', 'ditolak', 'ditutup']) && !\Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($permohonan->tanggal_jatuh_tempo))) {
            return back()->with('error', 'Permohonan ini belum memenuhi syarat untuk diajukan keberatan.');
        }

        $keberatan = \App\Models\PengajuanKeberatan::create([
            'permohonan_informasi_id' => $permohonan_id,
            'alasan_keberatan' => $request->alasan_keberatan,
            'keterangan_tambahan' => $request->keterangan_tambahan,
            'status' => 'Masuk'
        ]);

        \App\Models\PermohonanLog::create([
            'permohonan_informasi_id' => $permohonan_id,
            'user_id' => auth()->id(),
            'tahapan_proses' => 'Keberatan',
            'aksi' => 'Mengajukan Keberatan',
            'catatan' => 'Alasan: ' . $request->alasan_keberatan
        ]);

        return redirect()->route('admin.keberatan.index')->with('success', 'Pengajuan keberatan berhasil dibuat.');
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
        $keberatan->status = $request->status;
        
        if ($request->has('tanggapan_atasan')) {
            $keberatan->tanggapan_atasan = $request->tanggapan_atasan;
        }

        if (in_array($request->status, ['Selesai', 'Ditolak'])) {
            $keberatan->tanggal_selesai = now();
        }

        $keberatan->save();

        \App\Models\PermohonanLog::create([
            'permohonan_informasi_id' => $keberatan->permohonan_informasi_id,
            'user_id' => auth()->id(),
            'tahapan_proses' => 'Keberatan',
            'aksi' => 'Update Status Keberatan: ' . $request->status,
            'catatan' => $request->tanggapan_atasan ?? '-'
        ]);

        return back()->with('success', "Status keberatan berhasil diperbarui.");
    }
}
