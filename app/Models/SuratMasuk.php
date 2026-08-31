<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $fillable = [
        'nomor_surat', 'pengirim', 'tanggal_surat', 'tanggal_diterima', 
        'perihal', 'file_lampiran', 'status', 'klasifikasi_arsip_id'
    ];

    public function disposisis()
    {
        return $this->hasMany(Disposisi::class);
    }

    public function klasifikasi_arsip()
    {
        return $this->belongsTo(KlasifikasiArsip::class);
    }
}
