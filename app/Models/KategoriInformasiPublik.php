<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriInformasiPublik extends Model
{
    protected $fillable = ['nama_kategori', 'deskripsi', 'icon', 'bg_color', 'text_color'];

    public function informasiPubliks()
    {
        return $this->hasMany(InformasiPublik::class, 'kategori_informasi_publik_id');
    }

    public function informasi_publiks()
    {
        return $this->hasMany(InformasiPublik::class, 'kategori_informasi_publik_id');
    }
}
