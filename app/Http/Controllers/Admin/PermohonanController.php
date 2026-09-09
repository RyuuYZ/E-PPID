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
        $query = PermohonanInformasi::with(['kategori_pemohon', 'cara_memperoleh_informasi']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $permohonan = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $currentStatus = $request->status;

        return view('admin.permohonan.index', compact('permohonan', 'currentStatus'));
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
        $validated = $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'kategori_pemohon_id' => 'required|exists:kategori_pemohons,id',
            'nik_atau_no_badan_hukum' => 'required|string|max:50',
            'no_telp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'alamat' => 'required|string',
            'subjek' => 'required|string',
            'rincian_informasi' => 'required|string',
            'tujuan_penggunaan' => 'required|string',
            'cara_memperoleh_informasi_id' => 'required|exists:cara_memperoleh_informasis,id',
            'file_identitas' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($request->hasFile('file_identitas')) {
            // Using the same private storage path logic for admin as well if applicable, but standard public form uses 'local'. 
            // Wait, standard form uses 'identitas' in 'local'.
            $path = $request->file('file_identitas')->store('identitas', 'local');
            $validated['file_identitas'] = $path;
        }

        $validated['nomor_registrasi'] = 'REG-' . date('YmdHis') . '-' . rand(1000, 9999);
        $validated['rincian_informasi'] = $validated['subjek'] . "\n\n" . $validated['rincian_informasi'];
        $validated['status'] = \App\Enums\PermohonanStatus::Diajukan->value;

        $permohonan = \App\Models\PermohonanInformasi::create($validated);
        
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

        $path = storage_path('app/private/' . $permohonan->file_identitas);
        
        // Coba cek path lama jika file_identitas masih di public
        if (!file_exists($path)) {
            $path = storage_path('app/public/' . $permohonan->file_identitas);
        }

        if (!file_exists($path)) {
            abort(404, 'File identitas tidak ditemukan di server.');
        }

        return response()->file($path);
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
            'data_file' => 'required|file|max:10240',
            'catatan' => 'nullable|string',
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

            default => $user->hasRole('Super Admin'),
        };

        if (!$allowed) {
            abort(403, "Anda tidak memiliki izin untuk memperbarui status ke: {$target->label()}");
        }
    }
}

