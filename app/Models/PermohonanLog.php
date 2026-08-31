<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanLog extends Model
{
    protected $fillable = [
        'permohonan_informasi_id',
        'user_id',
        'tahapan_proses',
        'aksi',
        'catatan'
    ];

    public function permohonan_informasi()
    {
        return $this->belongsTo(PermohonanInformasi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
