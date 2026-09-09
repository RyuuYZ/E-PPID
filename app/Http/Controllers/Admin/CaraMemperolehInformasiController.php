<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CaraMemperolehInformasiController extends Controller
{
    private function checkAccess()
    {
        $user = auth()->user();
        if ($user->hasRole('Super Admin')) return;
        if (!$user->hasRole('Atasan PPID Pelaksana') && !$user->hasRole('PPID Pelaksana')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->checkAccess();
        $cara = \App\Models\CaraMemperolehInformasi::orderBy('nama_cara', 'asc')->paginate(10);
        return view('admin.cara_memperoleh_informasi.index', compact('cara'));
    }

    public function create()
    {
        $this->checkAccess();
        return view('admin.cara_memperoleh_informasi.create');
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        
        $validated = $request->validate([
            'nama_cara' => 'required|string|max:255|unique:cara_memperoleh_informasis,nama_cara',
            'deskripsi' => 'nullable|string'
        ]);

        \App\Models\CaraMemperolehInformasi::create($validated);

        return redirect()->route('admin.cara-memperoleh-informasi.index')->with('success', 'Cara Memperoleh Informasi berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $this->checkAccess();
        $cara = \App\Models\CaraMemperolehInformasi::findOrFail($id);
        return view('admin.cara_memperoleh_informasi.edit', compact('cara'));
    }

    public function update(Request $request, string $id)
    {
        $this->checkAccess();
        $cara = \App\Models\CaraMemperolehInformasi::findOrFail($id);
        
        $validated = $request->validate([
            'nama_cara' => 'required|string|max:255|unique:cara_memperoleh_informasis,nama_cara,' . $id,
            'deskripsi' => 'nullable|string'
        ]);

        $cara->update($validated);

        return redirect()->route('admin.cara-memperoleh-informasi.index')->with('success', 'Cara Memperoleh Informasi berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->checkAccess();
        $cara = \App\Models\CaraMemperolehInformasi::findOrFail($id);
        
        if ($cara->permohonan_informasis()->exists()) {
            return back()->with('error', 'Gagal menghapus data karena sedang digunakan oleh data permohonan.');
        }
        
        $cara->delete();

        return redirect()->route('admin.cara-memperoleh-informasi.index')->with('success', 'Cara Memperoleh Informasi berhasil dihapus.');
    }
}
