<?php

namespace App\Models;

use App\Enums\PermohonanStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermohonanInformasi extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nomor_registrasi', 'nama_pemohon', 'kategori_pemohon_id',
        'nik_atau_no_badan_hukum', 'pekerjaan', 'alamat', 'email', 'no_telp',
        'rincian_informasi', 'subjek_informasi', 'tujuan_penggunaan',
        'cara_memperoleh_informasi_id', 'cara_mendapatkan_salinan',
        'file_identitas', 'status', 'tanggal_selesai',
        'unit_pengolah_id', 'desk_layanan_id', 'ppid_pelaksana_id',
        'petugas_penghubung_id', 'keterangan_tidak_lengkap', 'jawaban_informasi',
        'file_jawaban', 'tanggal_jatuh_tempo',
        // New workflow columns
        'batas_waktu_lengkapi', 'batas_waktu_jawaban', 'diperpanjang',
        'surat_jawaban_path', 'ditandatangani_oleh', 'ditandatangani_at',
        'dikirim_at', 'ditutup_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PermohonanStatus::class,
            'tanggal_selesai' => 'datetime',
            'tanggal_jatuh_tempo' => 'date',
            'batas_waktu_lengkapi' => 'datetime',
            'batas_waktu_jawaban' => 'datetime',
            'ditandatangani_at' => 'datetime',
            'dikirim_at' => 'datetime',
            'ditutup_at' => 'datetime',
            'diperpanjang' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────────

    public function unit_pengolah()
    {
        return $this->belongsTo(UnitPengolah::class);
    }

    public function kategori_pemohon()
    {
        return $this->belongsTo(KategoriPemohon::class);
    }

    public function cara_memperoleh_informasi()
    {
        return $this->belongsTo(CaraMemperolehInformasi::class);
    }

    public function deskLayanan()
    {
        return $this->belongsTo(User::class, 'desk_layanan_id');
    }

    public function ppidPelaksana()
    {
        return $this->belongsTo(User::class, 'ppid_pelaksana_id');
    }

    public function ditandatanganiOleh()
    {
        return $this->belongsTo(User::class, 'ditandatangani_oleh');
    }

    public function logs()
    {
        return $this->hasMany(PermohonanLog::class)->orderBy('created_at', 'desc');
    }

    public function keberatan()
    {
        return $this->hasOne(PengajuanKeberatan::class);
    }

    public function penugasan()
    {
        return $this->hasMany(PenugasanPetugasPenghubung::class);
    }

    // ── Helpers ────────────────────────────────────────────────

    /**
     * Check if all penugasan have been validated (hasil_uji = sesuai).
     */
    public function allPenugasanSesuai(): bool
    {
        if ($this->penugasan->isEmpty()) {
            return false;
        }

        return $this->penugasan->every(
            fn($p) => $p->hasil_uji === \App\Enums\HasilUji::Sesuai
        );
    }
}
