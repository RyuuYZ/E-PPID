<?php

namespace Database\Seeders;

use App\Models\PermohonanInformasi;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermohonanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nomor_registrasi' => 'REG-' . date('Ym') . '-001',
                'nama_pemohon' => 'LSM Peduli Pembangunan',
                'kategori_pemohon_id' => 3, // Badan Hukum
                'rincian_informasi' => 'Dokumen Perencanaan Pembangunan Jembatan',
                'tujuan_penggunaan' => 'Penelitian dan Evaluasi Publik',
                'cara_memperoleh_informasi_id' => 2, // Email
                'status' => 'menunggu_data',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'nomor_registrasi' => 'REG-' . date('Ym') . '-002',
                'nama_pemohon' => 'Universitas Galuh (Penelitian)',
                'kategori_pemohon_id' => 3,
                'rincian_informasi' => 'Data Statistik Pengangguran 2025',
                'tujuan_penggunaan' => 'Skripsi / Tesis',
                'cara_memperoleh_informasi_id' => 2,
                'status' => 'siap_validasi',
                'created_at' => Carbon::now()->subDays(4),
            ],
            [
                'nomor_registrasi' => 'REG-' . date('Ym') . '-003',
                'nama_pemohon' => 'Ahmad Subagja',
                'kategori_pemohon_id' => 1, // Perorangan
                'rincian_informasi' => 'Salinan DPA Bappeda TA 2026',
                'tujuan_penggunaan' => 'Analisis Anggaran Daerah',
                'cara_memperoleh_informasi_id' => 1, // Langsung
                'status' => 'masuk',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'nomor_registrasi' => 'REG-' . date('Ym') . '-004',
                'nama_pemohon' => 'Budi Santoso',
                'kategori_pemohon_id' => 1,
                'rincian_informasi' => 'Laporan Kinerja Instansi Pemerintah (LKjIP) 2025',
                'tujuan_penggunaan' => 'Kajian Pribadi',
                'cara_memperoleh_informasi_id' => 3, // Hardcopy
                'status' => 'menunggu_koordinasi',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'nomor_registrasi' => 'REG-' . date('Ym') . '-005',
                'nama_pemohon' => 'Media Ciamis Ekspres',
                'kategori_pemohon_id' => 3,
                'rincian_informasi' => 'Daftar Proyek Infrastruktur Prioritas 2026',
                'tujuan_penggunaan' => 'Bahan Berita / Jurnalistik',
                'cara_memperoleh_informasi_id' => 2,
                'status' => 'menunggu_ttd',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'nomor_registrasi' => 'REG-' . date('Ym') . '-006',
                'nama_pemohon' => 'Koperasi Sejahtera',
                'kategori_pemohon_id' => 3,
                'rincian_informasi' => 'Data Bantuan UMKM 2024',
                'tujuan_penggunaan' => 'Pemetaan Usaha',
                'cara_memperoleh_informasi_id' => 1,
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(10),
                'tanggal_selesai' => Carbon::now()->subDays(2),
            ],
            [
                'nomor_registrasi' => 'REG-' . date('Ym') . '-007',
                'nama_pemohon' => 'Siti Aminah',
                'kategori_pemohon_id' => 1,
                'rincian_informasi' => 'Rencana Detail Tata Ruang (RDTR) Kecamatan Ciamis',
                'tujuan_penggunaan' => 'Keperluan Perizinan',
                'cara_memperoleh_informasi_id' => 2,
                'status' => 'menunggu_koordinasi',
                'created_at' => Carbon::now()->subDays(8),
            ],
        ];

        foreach ($data as $item) {
            PermohonanInformasi::updateOrCreate(
                ['nomor_registrasi' => $item['nomor_registrasi']],
                $item
            );
        }
    }
}
