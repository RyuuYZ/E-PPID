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

        if ($targetTahapan == 'Diverifikasi' && !$user->hasRole('Desk Layanan')) {
            abort(403, 'Hanya Desk Layanan yang dapat memverifikasi kelengkapan.');
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
            $permohonan->status = 'selesai'; // Jaga backward compatibility
        } elseif ($request->tahapan_proses == 'Ditutup') {
            $permohonan->status = 'ditutup';
        } else {
            $permohonan->status = 'diproses';
        }

        $permohonan->save();

        return back()->with('success', "Tahapan permohonan berhasil diperbarui menjadi: " . $request->tahapan_proses);
    }
}
