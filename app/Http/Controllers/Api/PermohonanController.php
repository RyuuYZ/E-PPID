<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanInformasi;
use App\Models\KategoriPemohon;
use App\Models\CaraMemperolehInformasi;
use App\Services\PermohonanStatusService;
use App\Enums\PermohonanStatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PermohonanController extends Controller
{
    protected $statusService;

    public function __construct(PermohonanStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    /**
     * Get reference data for the public submission form.
     */
    public function referensi()
    {
        return response()->json([
            'kategori_pemohon' => KategoriPemohon::all(),
            'cara_memperoleh_informasi' => CaraMemperolehInformasi::all(),
        ]);
    }

    /**
     * Submit a new Permohonan Informasi (Public Endpoint).
     */
    public function store(Request $request)
    {
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

        $validator = Validator::make($request->all(), [
            'nama_pemohon' => 'required|string|max:255',
            'kategori_pemohon_id' => 'required|exists:kategori_pemohons,id',
            'nik_atau_no_badan_hukum' => ['required', 'regex:/^[0-9]{1,16}$/'],
            'no_telp' => ['required', 'regex:/^\+62[0-9]{8,15}$/'],
            'email' => 'required|email|max:255',
            'alamat' => 'required|string',
            'subjek_informasi' => 'required|string',
            'rincian_informasi' => 'required|string',
            'tujuan_penggunaan' => 'required|string',
            'cara_memperoleh_informasi_id' => 'required|exists:cara_memperoleh_informasis,id',
            'file_identitas' => ['required', 'file', \App\Rules\SecureFile::identitas()],
        ], [
            'nik_atau_no_badan_hukum.required' => 'NIK / No. Identitas wajib diisi.',
            'nik_atau_no_badan_hukum.regex' => 'NIK harus berupa angka dan tidak boleh lebih dari 16 angka.',
            'no_telp.required' => 'Nomor telepon wajib diisi.',
            'no_telp.regex' => 'Nomor telepon harus diawali dengan +62 dan hanya berisi angka yang valid.',
            'file_identitas.required' => 'File identitas wajib diunggah.',
            'file_identitas.file' => 'File identitas harus berupa file yang valid.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        if ($request->hasFile('file_identitas')) {
            $path = $request->file('file_identitas')->store('identitas', 'public');
            $validated['file_identitas'] = $path;
        }

        $validated['nomor_registrasi'] = 'REG-' . date('YmdHis') . '-' . rand(1000, 9999);
        $validated['status'] = PermohonanStatus::Diajukan->value;

        $permohonan = PermohonanInformasi::create($validated);

        try {
            \Illuminate\Support\Facades\Mail::to($permohonan->email)->send(new \App\Mail\PermohonanTerkirimMail($permohonan));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim email permohonan API: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Permohonan berhasil disubmit',
            'nomor_registrasi' => $permohonan->nomor_registrasi,
            'download_tanda_terima_url' => route('permohonan.tanda_terima', $permohonan->nomor_registrasi)
        ], 201);
    }

    /**
     * Track a Permohonan by Registration Number (Public Endpoint).
     */
    public function lacak($nomor_registrasi)
    {
        $permohonan = PermohonanInformasi::with('logs')
            ->where('nomor_registrasi', $nomor_registrasi)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan'], 404);
        }

        return response()->json([
            'nomor_registrasi' => $permohonan->nomor_registrasi,
            'status' => $permohonan->status->value,
            'status_label' => $permohonan->status->label(),
            'tanggal_pengajuan' => $permohonan->created_at->format('Y-m-d H:i:s'),
            'subjek' => $permohonan->subjek_informasi,
            'rincian' => $permohonan->rincian_informasi,
            'is_terminal' => $permohonan->status->isTerminal(),
            'logs' => $permohonan->logs->map(function ($log) {
                return [
                    'aksi' => $log->aksi,
                    'catatan' => $log->catatan,
                    'waktu' => $log->created_at->format('Y-m-d H:i:s')
                ];
            })
        ]);
    }

    /**
     * Get list of Permohonan for Internal Admin App (Auth required).
     */
    public function indexAdmin(Request $request)
    {
        $user = $request->user();
        $query = PermohonanInformasi::query()->latest();

        // Enforce Policy via query scope logic
        if ($user->hasRole('Petugas Penghubung')) {
            $query->whereHas('penugasan', function ($q) use ($user) {
                $q->where('petugas_penghubung_id', $user->id);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $permohonan = $query->paginate(15);

        return response()->json($permohonan);
    }

    /**
     * Get detail of Permohonan for Internal Admin App (Auth required).
     */
    public function showAdmin(Request $request, $id)
    {
        $permohonan = PermohonanInformasi::with(['penugasan.unitPengolah', 'penugasan.petugasPenghubung', 'logs.user'])->findOrFail($id);
        
        $this->authorize('view', $permohonan);

        // Hide sensitive data if user cannot view it
        if (!$request->user()->can('viewSensitiveData', $permohonan)) {
            $permohonan->nik_atau_no_badan_hukum = null;
            $permohonan->alamat = null;
            $permohonan->file_identitas = null;
        }

        return response()->json($permohonan);
    }

    /**
     * Update state transition for a Permohonan via API (Auth required).
     */
    public function transitionAdmin(Request $request, $id)
    {
        $permohonan = PermohonanInformasi::findOrFail($id);
        $targetStatus = PermohonanStatus::tryFrom($request->input('target_status'));

        if (!$targetStatus) {
            return response()->json(['message' => 'Target status invalid'], 400);
        }

        try {
            // Desk Layanan Verifikasi
            if ($targetStatus === PermohonanStatus::Diverifikasi || $targetStatus === PermohonanStatus::MenungguKelengkapan) {
                $this->authorize('verify', $permohonan);
                $this->statusService->transition($permohonan, $targetStatus, $request->user(), [
                    'catatan' => $request->input('catatan', 'Verifikasi dari API')
                ]);
            }
            // PPID Draft Answer -> TTE
            elseif ($targetStatus === PermohonanStatus::MenungguTandaTangan) {
                $this->authorize('draftAnswer', $permohonan);
                $permohonan->surat_jawaban_path = $request->input('surat_jawaban_path', $permohonan->surat_jawaban_path);
                $permohonan->save();
                
                $this->statusService->transition($permohonan, $targetStatus, $request->user(), [
                    'catatan' => 'Draf jawaban diajukan ke atasan via API'
                ]);
            }
            // Atasan Sign
            elseif ($targetStatus === PermohonanStatus::Ditandatangani) {
                $this->authorize('sign', $permohonan);
                $permohonan->ditandatangani_oleh = $request->user()->id;
                $permohonan->ditandatangani_at = now();
                $permohonan->save();

                $this->statusService->transition($permohonan, $targetStatus, $request->user(), [
                    'catatan' => 'Surat ditandatangani elektronik via API'
                ]);
            }
            // Desk Layanan Finalize
            elseif ($targetStatus === PermohonanStatus::Selesai) {
                $this->authorize('sendAnswer', $permohonan);
                $permohonan->dikirim_at = now();
                $permohonan->save();

                $this->statusService->transition($permohonan, $targetStatus, $request->user(), [
                    'catatan' => 'Jawaban dikirimkan ke pemohon via API'
                ]);
            }
            else {
                return response()->json(['message' => 'Transisi status tidak diizinkan via endpoint ini atau status tidak dikenali'], 403);
            }

            return response()->json([
                'message' => 'Status berhasil diubah',
                'current_status' => $permohonan->status->value
            ]);

        } catch (\App\Exceptions\InvalidStatusTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }
}
