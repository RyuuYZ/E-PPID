<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaraMemperolehInformasi extends Model
{
    protected $fillable = ['nama_cara', 'deskripsi'];

    public function permohonan_informasis()
    {
        return $this->hasMany(PermohonanInformasi::class, 'cara_memperoleh_informasi_id');
    }
}
