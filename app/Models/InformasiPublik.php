<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformasiPublik extends Model
{
    protected $fillable = [
        'judul',
        'kategori_informasi_publik_id',
        'jenis_dokumen',
        'tahun',
        'ringkasan',
        'penanggung_jawab',
        'unit_pengolah_id',
        'file_path',
        'file_size',
        'tipe_media',
        'is_active',
        'download_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'download_count' => 'integer',
        'tahun' => 'integer',
    ];

    public static function getJenisDokumenOptions(): array
    {
        return [
            // Jangka Panjang & Menengah
            'RPJPD' => 'RPJPD (Rencana Pembangunan Jangka Panjang Daerah)',
            'RPJMD' => 'RPJMD (Rencana Pembangunan Jangka Menengah Daerah)',
            'Renstra' => 'Renstra (Rencana Strategis Perangkat Daerah)',
            // Dokumen Kerja Tahunan
            'RKPD' => 'RKPD (Rencana Kerja Pemerintah Daerah)',
            'Renja' => 'Renja (Rencana Kerja Perangkat Daerah)',
            'Musrenbang' => 'Dokumen Hasil Musrenbang',
            // Tata Ruang & Wilayah
            'RTRW' => 'RTRW (Rencana Tata Ruang Wilayah)',
            'RDTR' => 'RDTR (Rencana Detail Tata Ruang)',
            // Riset, Evaluasi & Inovasi (Bapperida)
            'Kajian & Riset' => 'Hasil Riset & Kajian Makroekonomi/Sosial',
            'Evaluasi Pembangunan' => 'Laporan Evaluasi Pembangunan Daerah',
            'Roadmap SIDa' => 'Roadmap Sistem Inovasi Daerah (SIDa)',
            // Regulasi & Profil Lembaga
            'Regulasi Daerah' => 'Regulasi & Peraturan Bupati Terkait',
            'Laporan Kinerja' => 'LKjIP / Laporan Kinerja Instansi',
            'Laporan Keuangan' => 'Laporan Operasional Keuangan',
            'Daftar Aset' => 'Daftar Aset & Inventaris Lembaga',
            'Profil Lembaga' => 'Profil & Struktur Organisasi Lembaga',
            'Lainnya' => 'Dokumen Informasi Publik Lainnya',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriInformasiPublik::class, 'kategori_informasi_publik_id');
    }

    public function unitPengolah(): BelongsTo
    {
        return $this->belongsTo(UnitPengolah::class, 'unit_pengolah_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('judul', 'like', "%{$term}%")
              ->orWhere('ringkasan', 'like', "%{$term}%")
              ->orWhere('jenis_dokumen', 'like', "%{$term}%")
              ->orWhere('penanggung_jawab', 'like', "%{$term}%");
        });
    }
}
