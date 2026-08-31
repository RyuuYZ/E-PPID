<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    protected $fillable = [
        'surat_masuk_id', 'pemberi_tugas_id', 'unit_pengolah_id', 
        'instruksi', 'status_disposisi'
    ];

    public function surat_masuk()
    {
        return $this->belongsTo(SuratMasuk::class);
    }

    public function pemberi_tugas()
    {
        return $this->belongsTo(User::class, 'pemberi_tugas_id');
    }

    public function unit_pengolah()
    {
        return $this->belongsTo(UnitPengolah::class, 'unit_pengolah_id');
    }
}
