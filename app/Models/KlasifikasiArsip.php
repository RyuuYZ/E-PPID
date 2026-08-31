<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlasifikasiArsip extends Model
{
    protected $fillable = ['kode', 'nama_klasifikasi', 'keterangan'];

    public function surat_masuks()
    {
        return $this->hasMany(SuratMasuk::class);
    }

    public function surat_keluars()
    {
        return $this->hasMany(SuratKeluar::class);
    }
}
