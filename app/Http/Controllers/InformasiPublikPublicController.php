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
        $query = auth()->check() ? InformasiPublik::query() : InformasiPublik::active();
        $dokumen = $query->with(['kategori', 'unitPengolah'])->findOrFail($id);

        $isInline = (bool) request()->query('inline');

        if (!$isInline) {
            $dokumen->increment('download_count');
        }

        $filename = \Illuminate\Support\Str::slug($dokumen->judul) . '.' . ($dokumen->tipe_media ? strtolower($dokumen->tipe_media) : 'pdf');

        if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
            $size = Storage::disk('public')->size($dokumen->file_path);
            if ($size > 500) {
                if ($isInline) {
                    return Storage::disk('public')->response($dokumen->file_path, $filename, [
                        'Content-Disposition' => "inline; filename=\"{$filename}\"",
                        'Content-Type' => 'application/pdf',
                    ]);
                }
                return Storage::disk('public')->download($dokumen->file_path, $filename);
            }
        }

        // Render official formatted PDF preview via DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('informasi_publik.preview_pdf', compact('dokumen'));
        $pdf->setPaper('A4', 'portrait');

        if ($isInline) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
