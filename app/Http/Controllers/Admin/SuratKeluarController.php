<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SuratKeluar;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    public function index()
    {
        $suratKeluars = SuratKeluar::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.surat_keluar.index', compact('suratKeluars'));
    }

    public function create()
    {
        $klasifikasiArsips = \App\Models\KlasifikasiArsip::all();
        return view('admin.surat_keluar.create', compact('klasifikasiArsips'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tujuan' => 'required|string|max:255',
            'perihal' => 'required|string',
            'file_draft' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'klasifikasi_arsip_id' => 'nullable|exists:klasifikasi_arsips,id'
        ]);

        if ($request->hasFile('file_draft')) {
            $path = $request->file('file_draft')->store('surat_keluar/draft', 'public');
            $validated['file_draft'] = $path;
        }

        SuratKeluar::create($validated);

        return redirect()->route('admin.surat-keluar.index')->with('success', 'Draft Surat Keluar berhasil disimpan.');
    }

    public function approveTte(Request $request, SuratKeluar $suratKeluar)
    {
        $user = auth()->user();
        if (!$user->hasRole('Atasan PPID Pelaksana')) {
            abort(403, 'Unauthorized action.');
        }

        $suratKeluar->update([
            'status' => 'Terkirim',
            'penandatangan_id' => $user->id,
            'ditandatangani_pada' => now(),
            'kode_verifikasi' => (string) \Illuminate\Support\Str::uuid(),
            'tanggal_keluar' => now()
        ]);

        return back()->with('success', 'Surat Keluar berhasil ditandatangani secara elektronik.');
    }
}
