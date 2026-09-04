<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanKeberatan extends Model
{
    protected $fillable = [
        'permohonan_informasi_id',
        'alasan_keberatan',
        'keterangan_tambahan',
        'bukti_pendukung_path',
        'tanggapan_atasan',
        'status',
        'tanggal_selesai',
        'diputuskan_oleh',
        'diputuskan_at',
        'batas_waktu_respon',
    ];

    protected function casts(): array
    {
        return [
            'status' => \App\Enums\KeberatanStatus::class,
            'tanggal_selesai' => 'datetime',
            'diputuskan_at' => 'datetime',
            'batas_waktu_respon' => 'datetime',
        ];
    }

    public function permohonan_informasi()
    {
        return $this->belongsTo(PermohonanInformasi::class);
    }

    public function diputuskanOleh()
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }
}

