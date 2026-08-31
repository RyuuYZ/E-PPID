<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanInformasi extends Model
{
    protected $fillable = [
        'nomor_registrasi', 'nama_pemohon', 'kategori_pemohon_id', 
        'nik_atau_no_badan_hukum', 'pekerjaan', 'alamat', 'email', 'no_telp',
        'rincian_informasi', 'tujuan_penggunaan', 'cara_memperoleh_informasi_id',
        'cara_mendapatkan_salinan', 'file_identitas', 'status', 'tanggal_selesai',
        'tahapan_proses', 'unit_pengolah_id', 'desk_layanan_id', 'ppid_pelaksana_id', 
        'petugas_penghubung_id', 'keterangan_tidak_lengkap', 'jawaban_informasi', 
        'file_jawaban', 'tanggal_jatuh_tempo'
    ];

    public function unit_pengolah()
    {
        return $this->belongsTo(UnitPengolah::class);
    }

    public function kategori_pemohon()
    {
        return $this->belongsTo(KategoriPemohon::class);
    }

    public function cara_memperoleh_informasi()
    {
        return $this->belongsTo(CaraMemperolehInformasi::class);
    }
}
