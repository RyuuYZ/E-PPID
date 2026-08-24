<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanInformasi extends Model
{
    protected $fillable = [
        'nomor_registrasi', 'nama_pemohon', 'kategori_pemohon', 'nik_atau_no_badan_hukum',
        'email', 'no_telp', 'alamat', 'pekerjaan', 'file_identitas', 'rincian_informasi',
        'tujuan_penggunaan', 'cara_memperoleh_informasi', 'cara_mendapatkan_salinan',
        'status', 'desk_layanan_id', 'ppid_pelaksana_id', 'petugas_penghubung_id',
        'keterangan_tidak_lengkap', 'jawaban_informasi', 'file_jawaban',
        'tanggal_jatuh_tempo', 'tanggal_selesai'
    ];
}
