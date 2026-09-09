<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPemohon extends Model
{
    protected $fillable = ['nama_kategori'];

    public function permohonan_informasis()
    {
        return $this->hasMany(PermohonanInformasi::class, 'kategori_pemohon_id');
    }
}
