<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InformasiPublik;
use App\Models\KategoriInformasiPublik;
use App\Models\UnitPengolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InformasiPublikController extends Controller
{
    public function index(Request $request)
    {
        $query = InformasiPublik::with(['kategori', 'unitPengolah']);

        if ($request->filled('kategori')) {
            $query->where('kategori_informasi_publik_id', $request->kategori);
        }

        if ($request->filled('jenis') && $request->jenis !== 'all') {
            $query->where('jenis_dokumen', $request->jenis);
        }

        if ($request->filled('tahun') && $request->tahun !== 'all') {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('q')) {
            $query->search($request->q);
        }

        $dokumen = $query->orderBy('tahun', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(10)
                         ->withQueryString();

        $kategoriList = KategoriInformasiPublik::where('nama_kategori', '!=', 'Keberatan')->get();
        $jenisOptions = InformasiPublik::getJenisDokumenOptions();
        $availableYears = InformasiPublik::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $totalDokumen = InformasiPublik::count();
        $totalUnduhan = InformasiPublik::sum('download_count');
        $totalAktif = InformasiPublik::where('is_active', true)->count();

        return view('admin.informasi_publik.index', compact(
            'dokumen',
            'kategoriList',
            'jenisOptions',
            'availableYears',
            'totalDokumen',
            'totalUnduhan',
            'totalAktif'
        ));
    }

    public function create()
    {
        $kategoriList = KategoriInformasiPublik::where('nama_kategori', '!=', 'Keberatan')->get();
        $unitPengolahList = UnitPengolah::all();
        $jenisOptions = InformasiPublik::getJenisDokumenOptions();

        return view('admin.informasi_publik.create', compact('kategoriList', 'unitPengolahList', 'jenisOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_informasi_publik_id' => 'required|exists:kategori_informasi_publiks,id',
            'jenis_dokumen' => 'required|string|max:100',
            'tahun' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'ringkasan' => 'nullable|string',
            'penanggung_jawab' => 'nullable|string|max:255',
            'unit_pengolah_id' => 'nullable|exists:unit_pengolahs,id',
            'file_dokumen' => 'nullable|file|mimes:pdf|max:30720', // Max 30MB
            'is_active' => 'nullable|boolean',
        ]);

        $filePath = null;
        $fileSize = null;

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filePath = $file->store('dokumen_publik', 'public');
            $bytes = $file->getSize();
            $fileSize = $this->formatBytes($bytes);
        }

        InformasiPublik::create([
            'judul' => $validated['judul'],
            'kategori_informasi_publik_id' => $validated['kategori_informasi_publik_id'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'tahun' => $validated['tahun'],
            'ringkasan' => $validated['ringkasan'] ?? null,
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? 'Bapperida Kabupaten Ciamis',
            'unit_pengolah_id' => $validated['unit_pengolah_id'] ?? null,
            'file_path' => $filePath,
            'file_size' => $fileSize ?? 'PDF',
            'tipe_media' => 'PDF',
            'is_active' => $request->has('is_active'),
            'download_count' => 0,
        ]);

        return redirect()->route('admin.informasi-publik.index')
                         ->with('success', 'Dokumen Informasi Publik berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $dokumen = InformasiPublik::findOrFail($id);
        $kategoriList = KategoriInformasiPublik::where('nama_kategori', '!=', 'Keberatan')->get();
        $unitPengolahList = UnitPengolah::all();
        $jenisOptions = InformasiPublik::getJenisDokumenOptions();

        return view('admin.informasi_publik.edit', compact('dokumen', 'kategoriList', 'unitPengolahList', 'jenisOptions'));
    }

    public function update(Request $request, $id)
    {
        $dokumen = InformasiPublik::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_informasi_publik_id' => 'required|exists:kategori_informasi_publiks,id',
            'jenis_dokumen' => 'required|string|max:100',
            'tahun' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'ringkasan' => 'nullable|string',
            'penanggung_jawab' => 'nullable|string|max:255',
            'unit_pengolah_id' => 'nullable|exists:unit_pengolahs,id',
            'file_dokumen' => 'nullable|file|mimes:pdf|max:30720',
            'is_active' => 'nullable|boolean',
        ]);

        $dataToUpdate = [
            'judul' => $validated['judul'],
            'kategori_informasi_publik_id' => $validated['kategori_informasi_publik_id'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'tahun' => $validated['tahun'],
            'ringkasan' => $validated['ringkasan'] ?? null,
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? 'Bapperida Kabupaten Ciamis',
            'unit_pengolah_id' => $validated['unit_pengolah_id'] ?? null,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('file_dokumen')) {
            // Remove old file if exists
            if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }

            $file = $request->file('file_dokumen');
            $dataToUpdate['file_path'] = $file->store('dokumen_publik', 'public');
            $dataToUpdate['file_size'] = $this->formatBytes($file->getSize());
            $dataToUpdate['tipe_media'] = 'PDF';
        }

        $dokumen->update($dataToUpdate);

        return redirect()->route('admin.informasi-publik.index')
                         ->with('success', 'Dokumen Informasi Publik berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dokumen = InformasiPublik::findOrFail($id);

        if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        $dokumen->delete();

        return redirect()->route('admin.informasi-publik.index')
                         ->with('success', 'Dokumen Informasi Publik berhasil dihapus.');
    }

    private function formatBytes($bytes, $precision = 1)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
