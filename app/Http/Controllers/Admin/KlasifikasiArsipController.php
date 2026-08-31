<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KlasifikasiArsip;

class KlasifikasiArsipController extends Controller
{
    private function checkAccess()
    {
        $user = auth()->user();
        if (!$user->hasRole('Atasan PPID Pelaksana') && !$user->hasRole('PPID Pelaksana')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->checkAccess();
        $klasifikasiArsips = KlasifikasiArsip::orderBy('kode', 'asc')->paginate(15);
        return view('admin.klasifikasi_arsip.index', compact('klasifikasiArsips'));
    }

    public function create()
    {
        $this->checkAccess();
        return view('admin.klasifikasi_arsip.create');
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        
        $validated = $request->validate([
            'kode' => 'required|string|unique:klasifikasi_arsips,kode|max:255',
            'nama_klasifikasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string'
        ]);

        KlasifikasiArsip::create($validated);

        return redirect()->route('admin.klasifikasi-arsip.index')->with('success', 'Klasifikasi Arsip berhasil ditambahkan.');
    }

    public function edit(KlasifikasiArsip $klasifikasiArsip)
    {
        $this->checkAccess();
        return view('admin.klasifikasi_arsip.edit', compact('klasifikasiArsip'));
    }

    public function update(Request $request, KlasifikasiArsip $klasifikasiArsip)
    {
        $this->checkAccess();
        
        $validated = $request->validate([
            'kode' => 'required|string|max:255|unique:klasifikasi_arsips,kode,' . $klasifikasiArsip->id,
            'nama_klasifikasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string'
        ]);

        $klasifikasiArsip->update($validated);

        return redirect()->route('admin.klasifikasi-arsip.index')->with('success', 'Klasifikasi Arsip berhasil diperbarui.');
    }

    public function destroy(KlasifikasiArsip $klasifikasiArsip)
    {
        $this->checkAccess();
        
        if ($klasifikasiArsip->surat_masuks()->exists() || $klasifikasiArsip->surat_keluars()->exists()) {
            return back()->with('error', 'Gagal menghapus klasifikasi karena sedang digunakan oleh surat masuk/keluar.');
        }
        
        $klasifikasiArsip->delete();

        return redirect()->route('admin.klasifikasi-arsip.index')->with('success', 'Klasifikasi Arsip berhasil dihapus.');
    }
}
