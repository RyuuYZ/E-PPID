<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KategoriInformasiPublikController extends Controller
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
        $kategori = \App\Models\KategoriInformasiPublik::orderBy('nama_kategori', 'asc')->paginate(15);
        return view('admin.kategori_informasi_publik.index', compact('kategori'));
    }

    public function create()
    {
        $this->checkAccess();
        return view('admin.kategori_informasi_publik.create');
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_informasi_publiks,nama_kategori',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'bg_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:50'
        ]);

        \App\Models\KategoriInformasiPublik::create($validated);

        return redirect()->route('admin.kategori-informasi-publik.index')->with('success', 'Kategori Informasi Publik berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $this->checkAccess();
        $kategori = \App\Models\KategoriInformasiPublik::findOrFail($id);
        return view('admin.kategori_informasi_publik.edit', compact('kategori'));
    }

    public function update(Request $request, string $id)
    {
        $this->checkAccess();
        $kategori = \App\Models\KategoriInformasiPublik::findOrFail($id);
        
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_informasi_publiks,nama_kategori,' . $id,
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'bg_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:50'
        ]);

        $kategori->update($validated);

        return redirect()->route('admin.kategori-informasi-publik.index')->with('success', 'Kategori Informasi Publik berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->checkAccess();
        $kategori = \App\Models\KategoriInformasiPublik::findOrFail($id);
        
        $kategori->delete();

        return redirect()->route('admin.kategori-informasi-publik.index')->with('success', 'Kategori Informasi Publik berhasil dihapus.');
    }
}
