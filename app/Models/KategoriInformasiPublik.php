<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriInformasiPublik extends Model
{
    protected $fillable = ['nama_kategori', 'deskripsi', 'icon', 'bg_color', 'text_color'];
}
