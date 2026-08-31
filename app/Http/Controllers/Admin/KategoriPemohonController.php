<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KategoriPemohonController extends Controller
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
        $kategori = \App\Models\KategoriPemohon::orderBy('nama_kategori', 'asc')->paginate(15);
        return view('admin.kategori_pemohon.index', compact('kategori'));
    }

    public function create()
    {
        $this->checkAccess();
        return view('admin.kategori_pemohon.create');
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_pemohons,nama_kategori'
        ]);

        \App\Models\KategoriPemohon::create($validated);

        return redirect()->route('admin.kategori-pemohon.index')->with('success', 'Kategori Pemohon berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $this->checkAccess();
        $kategori = \App\Models\KategoriPemohon::findOrFail($id);
        return view('admin.kategori_pemohon.edit', compact('kategori'));
    }

    public function update(Request $request, string $id)
    {
        $this->checkAccess();
        $kategori = \App\Models\KategoriPemohon::findOrFail($id);
        
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_pemohons,nama_kategori,' . $id
        ]);

        $kategori->update($validated);

        return redirect()->route('admin.kategori-pemohon.index')->with('success', 'Kategori Pemohon berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->checkAccess();
        $kategori = \App\Models\KategoriPemohon::findOrFail($id);
        
        if ($kategori->permohonan_informasis()->exists()) {
            return back()->with('error', 'Gagal menghapus kategori karena sedang digunakan oleh data permohonan.');
        }
        
        $kategori->delete();

        return redirect()->route('admin.kategori-pemohon.index')->with('success', 'Kategori Pemohon berhasil dihapus.');
    }
}
