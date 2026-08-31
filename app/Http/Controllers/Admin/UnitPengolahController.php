<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\UnitPengolah;

class UnitPengolahController extends Controller
{
    private function checkAccess()
    {
        $user = auth()->user();
        if ($user->hasRole('Super Admin')) return;
        if (!$user->hasRole('Atasan PPID Pelaksana') && !$user->hasRole('PPID Pelaksana')) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola Master Data Bidang.');
        }
    }

    public function index()
    {
        $this->checkAccess();
        $unitPengolah = UnitPengolah::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.unit_pengolah.index', compact('unitPengolah'));
    }

    public function create()
    {
        $this->checkAccess();
        return view('admin.unit_pengolah.create');
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        
        $request->validate([
            'nama_bidang' => 'required|string|max:255|unique:unit_pengolahs,nama_bidang',
            'deskripsi' => 'nullable|string'
        ]);

        UnitPengolah::create($request->all());

        return redirect()->route('admin.unit-pengolah.index')->with('success', 'Data Bidang berhasil ditambahkan.');
    }

    public function edit(UnitPengolah $unitPengolah)
    {
        $this->checkAccess();
        return view('admin.unit_pengolah.edit', compact('unitPengolah'));
    }

    public function update(Request $request, UnitPengolah $unitPengolah)
    {
        $this->checkAccess();
        
        $request->validate([
            'nama_bidang' => 'required|string|max:255|unique:unit_pengolahs,nama_bidang,' . $unitPengolah->id,
            'deskripsi' => 'nullable|string'
        ]);

        $unitPengolah->update($request->all());

        return redirect()->route('admin.unit-pengolah.index')->with('success', 'Data Bidang berhasil diperbarui.');
    }

    public function destroy(UnitPengolah $unitPengolah)
    {
        $this->checkAccess();
        
        // Prevent deletion if it's already used in permohonan or users (Optional but good practice)
        if ($unitPengolah->users()->count() > 0 || $unitPengolah->permohonan_informasis()->count() > 0) {
            return redirect()->route('admin.unit-pengolah.index')->with('error', 'Bidang tidak dapat dihapus karena sedang digunakan oleh Pengguna atau Permohonan.');
        }

        $unitPengolah->delete();

        return redirect()->route('admin.unit-pengolah.index')->with('success', 'Data Bidang berhasil dihapus.');
    }
}
