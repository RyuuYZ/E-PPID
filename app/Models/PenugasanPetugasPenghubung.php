<?php

namespace App\Models;

use App\Enums\PenugasanStatus;
use App\Enums\HasilUji;
use Illuminate\Database\Eloquent\Model;

class PenugasanPetugasPenghubung extends Model
{
    protected $table = 'penugasan_petugas_penghubungs';

    protected $fillable = [
        'permohonan_informasi_id',
        'petugas_penghubung_id',
        'unit_pengolah_id',
        'ditugaskan_oleh',
        'instruksi',
        'batas_waktu',
        'status',
        'data_path',
        'catatan_petugas_penghubung',
        'diserahkan_at',
        'hasil_uji',
        'catatan_uji',
    ];

    protected function casts(): array
    {
        return [
            'batas_waktu' => 'datetime',
            'diserahkan_at' => 'datetime',
            'status' => PenugasanStatus::class,
            'hasil_uji' => HasilUji::class,
        ];
    }

    public function permohonan()
    {
        return $this->belongsTo(PermohonanInformasi::class, 'permohonan_informasi_id');
    }

    public function petugasPenghubung()
    {
        return $this->belongsTo(User::class, 'petugas_penghubung_id');
    }

    public function unitPengolah()
    {
        return $this->belongsTo(UnitPengolah::class, 'unit_pengolah_id');
    }

    public function ditugaskanOlehUser()
    {
        return $this->belongsTo(User::class, 'ditugaskan_oleh');
    }
}
