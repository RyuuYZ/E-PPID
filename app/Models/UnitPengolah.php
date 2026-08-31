<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitPengolah extends Model
{
    protected $fillable = ['nama_bidang', 'deskripsi'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permohonan_informasis()
    {
        return $this->hasMany(PermohonanInformasi::class);
    }
}
