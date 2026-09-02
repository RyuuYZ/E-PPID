<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanKeberatan extends Model
{
    protected $fillable = [
        'permohonan_informasi_id',
        'alasan_keberatan',
        'keterangan_tambahan',
        'tanggapan_atasan',
        'status',
        'tanggal_selesai'
    ];

    public function permohonan_informasi()
    {
        return $this->belongsTo(PermohonanInformasi::class);
    }
}
