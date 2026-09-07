<?php

namespace App\Http\Controllers;

use App\Models\InformasiPublik;
use App\Models\KategoriInformasiPublik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InformasiPublikPublicController extends Controller
{
    public function index(Request $request)
    {
        $query = InformasiPublik::with(['kategori', 'unitPengolah'])->active();

        // Filter UU KIP Kategori
        if ($request->filled('kategori')) {
            $kategoriId = $request->kategori;
            // Support searching by ID or nama_kategori
            if (!is_numeric($kategoriId)) {
                $kat = KategoriInformasiPublik::where('nama_kategori', 'like', "%{$kategoriId}%")->first();
                $kategoriId = $kat ? $kat->id : null;
            }
            if ($kategoriId) {
                $query->where('kategori_informasi_publik_id', $kategoriId);
            }
        }

        // Filter Jenis Dokumen Perencanaan Bapperida
        if ($request->filled('jenis') && $request->jenis !== 'all') {
            $query->where('jenis_dokumen', $request->jenis);
        }

        // Filter Tahun
        if ($request->filled('tahun') && $request->tahun !== 'all') {
            $query->where('tahun', $request->tahun);
        }

        // Search Keyword
        if ($request->filled('q')) {
            $query->search($request->q);
        }

        $dokumenList = $query->orderBy('tahun', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(9)
                            ->withQueryString();

        $kategoriList = KategoriInformasiPublik::where('nama_kategori', '!=', 'Keberatan')->get();
        $jenisOptions = InformasiPublik::getJenisDokumenOptions();
        $availableYears = InformasiPublik::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        // Total stats
        $totalDokumen = InformasiPublik::active()->count();
        $totalUnduhan = InformasiPublik::active()->sum('download_count');

        return view('informasi_publik.index', compact(
            'dokumenList',
            'kategoriList',
            'jenisOptions',
            'availableYears',
            'totalDokumen',
            'totalUnduhan'
        ));
    }

    public function download($id)
    {
        $dokumen = InformasiPublik::active()->findOrFail($id);

        $dokumen->increment('download_count');

        if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
            $filename = \Illuminate\Support\Str::slug($dokumen->judul) . '.' . ($dokumen->tipe_media ? strtolower($dokumen->tipe_media) : 'pdf');
            return Storage::disk('public')->download($dokumen->file_path, $filename);
        }

        // Fallback placeholder PDF generation if file is missing in disk
        $filename = \Illuminate\Support\Str::slug($dokumen->judul) . '.pdf';
        $content = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000052 00000 n \n0000000101 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";
        
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
