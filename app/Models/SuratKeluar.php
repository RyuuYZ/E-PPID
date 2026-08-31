<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    protected $fillable = [
        'nomor_surat', 'tujuan', 'perihal', 'tanggal_keluar', 
        'file_draft', 'file_final', 'status',
        'penandatangan_id', 'ditandatangani_pada', 'kode_verifikasi',
        'klasifikasi_arsip_id'
    ];

    public function penandatangan()
    {
        return $this->belongsTo(User::class, 'penandatangan_id');
    }

    public function klasifikasi_arsip()
    {
        return $this->belongsTo(KlasifikasiArsip::class);
    }
}
