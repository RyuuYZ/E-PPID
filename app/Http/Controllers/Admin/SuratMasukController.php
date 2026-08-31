<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SuratMasuk;
use Illuminate\Support\Facades\Storage;

class SuratMasukController extends Controller
{
    public function index()
    {
        $suratMasuks = SuratMasuk::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.surat_masuk.index', compact('suratMasuks'));
    }

    public function create()
    {
        $klasifikasiArsips = \App\Models\KlasifikasiArsip::all();
        return view('admin.surat_masuk.create', compact('klasifikasiArsips'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'required|date',
            'perihal' => 'required|string',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'klasifikasi_arsip_id' => 'nullable|exists:klasifikasi_arsips,id'
        ]);

        if ($request->hasFile('file_lampiran')) {
            $path = $request->file('file_lampiran')->store('surat_masuk', 'public');
            $validated['file_lampiran'] = $path;
        }

        SuratMasuk::create($validated);

        return redirect()->route('admin.surat-masuk.index')->with('success', 'Surat Masuk berhasil diregistrasi.');
    }

    public function show(SuratMasuk $suratMasuk)
    {
        $disposisis = $suratMasuk->disposisis()->with(['pemberi_tugas', 'unit_pengolah'])->get();
        $unitPengolahs = \App\Models\UnitPengolah::all();
        
        return view('admin.surat_masuk.show', compact('suratMasuk', 'disposisis', 'unitPengolahs'));
    }
    
    // Untuk Disposisi kita bisa letakkan fungsinya di sini atau di DisposisiController. Kita satukan saja di sini untuk kepraktisan Phase 2.
    public function disposisi(Request $request, SuratMasuk $suratMasuk)
    {
        $request->validate([
            'unit_pengolah_id' => 'required|exists:unit_pengolahs,id',
            'instruksi' => 'required|string'
        ]);

        \App\Models\Disposisi::create([
            'surat_masuk_id' => $suratMasuk->id,
            'pemberi_tugas_id' => auth()->id(),
            'unit_pengolah_id' => $request->unit_pengolah_id,
            'instruksi' => $request->instruksi,
            'status_disposisi' => 'Menunggu'
        ]);

        $suratMasuk->update(['status' => 'Didisposisikan']);

        return back()->with('success', 'Surat berhasil didisposisikan.');
    }
}
