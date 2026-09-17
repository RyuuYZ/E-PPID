<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermohonanStatus;
use App\Enums\PenugasanStatus;
use App\Enums\HasilUji;
use App\Http\Controllers\Controller;
use App\Models\PenugasanPetugasPenghubung;
use App\Models\PermohonanInformasi;
use App\Models\UnitPengolah;
use App\Models\User;
use App\Services\PermohonanStatusService;
use Illuminate\Http\Request;

class PermohonanController extends Controller
{
    public function __construct(
        private PermohonanStatusService $statusService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $permohonan = PermohonanInformasi::with(['kategori_pemohon', 'cara_memperoleh_informasi'])
            ->orderBy('created_at', 'desc')
            ->get();

        $currentStatus = $request->status ?? 'all';
        $kategoriPemohons = \App\Models\KategoriPemohon::all();
        $caraMemperoleh = \App\Models\CaraMemperolehInformasi::all();

        return view('admin.permohonan.index', compact('permohonan', 'currentStatus', 'kategoriPemohons', 'caraMemperoleh'));
    }

    /**
     * Display the specified resource.
     */
    public function create()
    {
        $kategoriPemohons = \App\Models\KategoriPemohon::all();
        $caraMemperoleh = \App\Models\CaraMemperolehInformasi::all();
        return view('admin.permohonan.create', compact('kategoriPemohons', 'caraMemperoleh'));
    }

    public function store(Request $request)
    {
        if (!$request->has('subjek_informasi') && $request->has('subjek')) {
            $request->merge(['subjek_informasi' => $request->input('subjek')]);
        }

        if ($request->filled('kecamatan') && $request->filled('desa')) {
            $detail = trim($request->input('detail_alamat', ''));
            $alamat = ($detail !== '' ? $detail . ', ' : '') . 'Desa/Kel. ' . $request->input('desa') . ', Kec. ' . $request->input('kecamatan') . ', Kab. Ciamis, Jawa Barat';
            $request->merge(['alamat' => $alamat]);
        }

        if ($request->filled('no_telp')) {
            $digits = preg_replace('/[^0-9]/', '', $request->input('no_telp'));
            if (str_starts_with($digits, '62')) {
                $formattedPhone = '+62' . substr($digits, 2);
            } elseif (str_starts_with($digits, '0')) {
                $formattedPhone = '+62' . substr($digits, 1);
            } else {
                $formattedPhone = '+62' . $digits;
            }
            $request->merge(['no_telp' => $formattedPhone]);
        }

        $validated = $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'kategori_pemohon_id' => 'required|exists:kategori_pemohons,id',
            'nik_atau_no_badan_hukum' => ['required', 'regex:/^[0-9]{1,16}$/'],
            'pekerjaan' => 'nullable|string|max:100',
            'no_telp' => ['required', 'regex:/^\+62[0-9]{8,15}$/'],
            'email' => 'required|email|max:255',
            'alamat' => 'required|string',
            'subjek_informasi' => 'required|string|max:255',
            'rincian_informasi' => 'required|string',
            'tujuan_penggunaan' => 'required|string',
            'cara_memperoleh_informasi_id' => 'required|exists:cara_memperoleh_informasis,id',
            'cara_mendapatkan_salinan' => 'nullable|string|max:50',
            'file_identitas' => ['required', 'file', \App\Rules\SecureFile::identitas()],
        ], [
            'nama_pemohon.required' => 'Nama lengkap pemohon wajib diisi.',
            'kategori_pemohon_id.required' => 'Kategori pemohon wajib dipilih.',
            'nik_atau_no_badan_hukum.required' => 'NIK / No. Identitas wajib diisi.',
            'nik_atau_no_badan_hukum.regex' => 'NIK harus berupa angka dan tidak boleh lebih dari 16 angka.',
            'no_telp.required' => 'Nomor telepon wajib diisi.',
            'no_telp.regex' => 'Nomor telepon harus diawali dengan +62 dan hanya berisi angka yang valid.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'subjek_informasi.required' => 'Judul / Subjek informasi wajib diisi.',
            'rincian_informasi.required' => 'Rincian / Isi informasi wajib diisi.',
            'tujuan_penggunaan.required' => 'Tujuan penggunaan informasi wajib diisi.',
            'cara_memperoleh_informasi_id.required' => 'Cara memperoleh informasi wajib dipilih.',
            'file_identitas.required' => 'File identitas wajib diunggah.',
            'file_identitas.file' => 'File identitas harus berupa file yang valid.'
        ]);

        if ($request->hasFile('file_identitas')) {
            $path = $request->file('file_identitas')->store('identitas', 'local');
            $validated['file_identitas'] = $path;
        }

        $validated['nomor_registrasi'] = 'REG-' . date('YmdHis') . '-' . rand(1000, 9999);
        $validated['status'] = \App\Enums\PermohonanStatus::Diajukan->value;

        $permohonan = \App\Models\PermohonanInformasi::create($validated);
        
        // Kirim email konfirmasi dan kode invoice jika email diisi
        if (!empty($permohonan->email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($permohonan->email)->send(new \App\Mail\PermohonanTerkirimMail($permohonan));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim email permohonan walk-in: ' . $e->getMessage());
            }
        }

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Create Permohonan Walk-in',
            'description' => "Membuat permohonan baru untuk: {$permohonan->nama_pemohon} ({$permohonan->nomor_registrasi})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.permohonan.index', ['status' => 'diajukan'])->with('success', 'Permohonan berhasil ditambahkan.');
    }
    public function show($id)
    {
        $permohonan = PermohonanInformasi::with(['penugasan.petugasPenghubung', 'penugasan.unitPengolah', 'logs'])->findOrFail($id);
        $unitPengolahs = UnitPengolah::all();
        $petugasPenghubungs = User::whereHas('role', function ($q) {
            $q->where('name', 'like', '%Petugas Penghubung%');
        })->get();

        return view('admin.permohonan.show', compact('permohonan', 'unitPengolahs', 'petugasPenghubungs'));
    }

    /**
     * View the identity file securely.
     */
    public function viewFileIdentitas($id)
    {
        $permohonan = PermohonanInformasi::findOrFail($id);
        
        if (!$permohonan->file_identitas) {
            abort(404, 'File identitas tidak ditemukan.');
        }

        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($permohonan->file_identitas)) {
            return \Illuminate\Support\Facades\Storage::disk('local')->response($permohonan->file_identitas, null, [
                'Content-Disposition' => 'inline',
            ]);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($permohonan->file_identitas)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->response($permohonan->file_identitas, null, [
                'Content-Disposition' => 'inline',
            ]);
        }

        $paths = [
            storage_path('app/private/' . $permohonan->file_identitas),
            storage_path('app/' . $permohonan->file_identitas),
            storage_path('app/public/' . $permohonan->file_identitas),
            public_path('storage/' . $permohonan->file_identitas),
        ];

        foreach ($paths as $path) {
            if (file_exists($path) && is_file($path)) {
                $mime = mime_content_type($path) ?: 'application/octet-stream';
                return response()->file($path, [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline',
                ]);
            }
        }

        abort(404, 'File identitas tidak ditemukan di server.');
    }

    /**
     * Update the status of the specified resource via the state machine.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'target_status' => 'required|string',
        ]);

        $user = auth()->user();
        $permohonan = PermohonanInformasi::findOrFail($id);
        $targetStatus = PermohonanStatus::from($request->target_status);

        // Authorization checks per target status
        $this->authorizeTransition($user, $targetStatus);

        $metadata = ['catatan' => $request->catatan ?? null];

        // Specific metadata per transition
        if ($targetStatus === PermohonanStatus::MenungguKelengkapan) {
            $metadata['catatan'] = $request->alasan_tidak_lengkap ?? 'Berkas tidak lengkap.';
        }

        if ($targetStatus === PermohonanStatus::MenungguTandaTangan) {
            $metadata['surat_jawaban_path'] = $request->surat_jawaban_path ?? null;
        }

        if ($targetStatus === PermohonanStatus::Ditandatangani) {
            $ttdPath = null;

            if ($request->use_saved_signature === '1') {
                if (!$user->signature_path) {
                    return back()->with('error', 'Anda tidak memiliki tanda tangan tersimpan.');
                }
                $ttdPath = $user->signature_path;
            } else if ($request->signature_data) {
                // Decode base64 image
                $image_parts = explode(";base64,", $request->signature_data);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = 'signature_' . time() . '_' . uniqid() . '.' . $image_type;
                $ttdPath = 'signatures/' . $filename;
                
                \Illuminate\Support\Facades\Storage::disk('public')->put($ttdPath, $image_base64);

                if ($request->has('save_signature') && $request->save_signature == '1') {
                    $user->update(['signature_path' => $ttdPath]);
                }
            } else {
                return back()->with('error', 'Tanda tangan wajib diisi.');
            }

            // Save to Permohonan record directly
            $permohonan->update(['ttd_path' => $ttdPath]);
            $metadata['catatan'] = 'Tanda tangan elektronik berhasil dibubuhkan.';
        }

        try {
            $this->statusService->transition($permohonan, $targetStatus, $user, $metadata);
            return back()->with('success', "Status berhasil diperbarui menjadi: {$targetStatus->label()}");
        } catch (\App\Exceptions\InvalidStatusTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Assign permohonan to one or more petugas penghubung.
     */
    public function assignPetugas(Request $request, $id)
    {
        $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.petugas_penghubung_id' => 'required|exists:users,id',
            'assignments.*.unit_pengolah_id' => 'required|exists:unit_pengolahs,id',
            'assignments.*.instruksi' => 'nullable|string',
            'assignments.*.batas_waktu' => 'nullable|date',
        ]);

        $user = auth()->user();
        if (!$user->hasRole('PPID Pelaksana')) {
            abort(403, 'Hanya PPID Pelaksana yang dapat menugaskan petugas.');
        }

        $permohonan = PermohonanInformasi::findOrFail($id);

        // Transition to Ditugaskan if currently Diverifikasi
        if ($permohonan->status === PermohonanStatus::Diverifikasi) {
            $this->statusService->transition($permohonan, PermohonanStatus::Ditugaskan, $user, [
                'catatan' => 'Ditugaskan ke ' . count($request->assignments) . ' unit pengolah.',
            ]);
        }

        // Create penugasan records
        foreach ($request->assignments as $assignment) {
            PenugasanPetugasPenghubung::create([
                'permohonan_informasi_id' => $permohonan->id,
                'petugas_penghubung_id' => $assignment['petugas_penghubung_id'],
                'unit_pengolah_id' => $assignment['unit_pengolah_id'],
                'ditugaskan_oleh' => $user->id,
                'instruksi' => $assignment['instruksi'] ?? null,
                'batas_waktu' => $assignment['batas_waktu'] ?? null,
                'status' => PenugasanStatus::Ditugaskan,
                'hasil_uji' => HasilUji::Pending,
            ]);
        }

        // Transition to MenungguData
        $permohonan->refresh();
        if ($permohonan->status === PermohonanStatus::Ditugaskan) {
            $this->statusService->transition($permohonan, PermohonanStatus::MenungguData, $user, [
                'catatan' => 'Menunggu data dari petugas penghubung.',
            ]);
        }

        return back()->with('success', 'Penugasan berhasil dibuat.');
    }

    /**
     * Petugas Penghubung submits data for their assignment.
     */
    public function submitData(Request $request, $penugasanId)
    {
        $request->validate([
            'data_file' => ['required', 'file', \App\Rules\SecureFile::penugasan()],
            'catatan' => 'nullable|string',
        ], [
            'data_file.required' => 'Berkas data hasil penugasan wajib dilampirkan.',
            'data_file.file' => 'Berkas data harus berupa file yang valid.',
        ]);

        $user = auth()->user();
        $penugasan = PenugasanPetugasPenghubung::findOrFail($penugasanId);

        // Ensure this petugas owns this assignment
        if ($penugasan->petugas_penghubung_id !== $user->id && !$user->hasRole('Super Admin')) {
            abort(403, 'Anda tidak memiliki akses ke penugasan ini.');
        }

        $path = $request->file('data_file')->store('penugasan_data', 'public');

        $penugasan->update([
            'data_path' => $path,
            'catatan_petugas_penghubung' => $request->catatan,
            'status' => PenugasanStatus::Diserahkan,
            'diserahkan_at' => now(),
        ]);

        // Check if all penugasan for this permohonan are now submitted
        $permohonan = $penugasan->permohonan;
        $allSubmitted = $permohonan->penugasan->every(
            fn($p) => $p->status === PenugasanStatus::Diserahkan
        );

        if ($allSubmitted && $permohonan->status === PermohonanStatus::MenungguData) {
            $this->statusService->transition($permohonan, PermohonanStatus::DataDiuji, null, [
                'catatan' => 'Semua data telah diserahkan oleh petugas penghubung.',
            ]);
        }

        return back()->with('success', 'Data berhasil diserahkan.');
    }

    /**
     * PPID Pelaksana reviews a penugasan submission.
     */
    public function reviewPenugasan(Request $request, $penugasanId)
    {
        $request->validate([
            'hasil_uji' => 'required|in:sesuai,perlu_revisi',
            'catatan_uji' => 'nullable|string',
        ]);

        $user = auth()->user();
        if (!$user->hasRole('PPID Pelaksana')) {
            abort(403, 'Hanya PPID Pelaksana yang dapat menguji data.');
        }

        $penugasan = PenugasanPetugasPenghubung::findOrFail($penugasanId);
        $penugasan->update([
            'hasil_uji' => HasilUji::from($request->hasil_uji),
            'catatan_uji' => $request->catatan_uji,
        ]);

        // If marked perlu_revisi, reset the penugasan status so petugas can resubmit
        if ($request->hasil_uji === 'perlu_revisi') {
            $penugasan->update([
                'status' => PenugasanStatus::Ditugaskan,
                'data_path' => null,
                'diserahkan_at' => null,
            ]);
        }

        return back()->with('success', 'Hasil pengujian berhasil disimpan.');
    }

    /**
     * Extend the answer deadline by 7 working days.
     */
    public function extendDeadline(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasRole('PPID Pelaksana')) {
            abort(403, 'Hanya PPID Pelaksana yang dapat memperpanjang batas waktu.');
        }

        $permohonan = PermohonanInformasi::findOrFail($id);

        try {
            $this->statusService->extendDeadline($permohonan, $user);
            return back()->with('success', 'Batas waktu jawaban berhasil diperpanjang (+7 hari kerja).');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Authorization guard based on target status.
     */
    private function authorizeTransition(User $user, PermohonanStatus $target): void
    {
        // Super Admin memiliki hak akses penuh untuk seluruh transisi alur layanan
        if ($user->hasRole('Super Admin')) {
            return;
        }

        $allowed = match ($target) {
            PermohonanStatus::Diverifikasi,
            PermohonanStatus::MenungguKelengkapan,
            PermohonanStatus::Selesai => $user->hasRole('Desk Layanan'),

            PermohonanStatus::Ditugaskan,
            PermohonanStatus::MenungguTandaTangan => $user->hasRole('PPID Pelaksana'),

            PermohonanStatus::DataDiuji,
            PermohonanStatus::MenungguData => $user->hasRole('Petugas Penghubung') || $user->hasRole('PPID Pelaksana'),

            PermohonanStatus::Ditandatangani => $user->hasRole('Atasan PPID Pelaksana'),

            PermohonanStatus::DitutupTidakLengkap => true, // System or any authorized user

            default => false,
        };

        if (!$allowed) {
            abort(403, "Anda tidak memiliki izin untuk memperbarui status ke: {$target->label()}");
        }
    }
}

