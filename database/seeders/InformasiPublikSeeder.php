<?php

namespace Database\Seeders;

use App\Models\InformasiPublik;
use App\Models\KategoriInformasiPublik;
use App\Models\UnitPengolah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class InformasiPublikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $katBerkala = KategoriInformasiPublik::where('nama_kategori', 'Informasi Berkala')->first();
        $katSetiapSaat = KategoriInformasiPublik::where('nama_kategori', 'Setiap Saat')->first();
        $katSertaMerta = KategoriInformasiPublik::where('nama_kategori', 'Serta Merta')->first();

        // Ambil unit pengolah jika ada
        $unitPerencanaan = UnitPengolah::where('nama_bidang', 'like', '%Perencanaan%')->first();
        $unitLitbang = UnitPengolah::where('nama_bidang', 'like', '%Litbang%')->orWhere('nama_bidang', 'like', '%Riset%')->first();
        $unitInfrastruktur = UnitPengolah::where('nama_bidang', 'like', '%Infrastruktur%')->orWhere('nama_bidang', 'like', '%Tata Ruang%')->first();
        $unitSekretariat = UnitPengolah::where('nama_bidang', 'like', '%Sekretariat%')->first();

        // Ensure storage directory exists
        if (!Storage::disk('public')->exists('dokumen_publik')) {
            Storage::disk('public')->makeDirectory('dokumen_publik');
        }

        // Create sample placeholder PDF if not exists
        $samplePdfPath = 'dokumen_publik/sample_dokumen_bapperida.pdf';
        if (!Storage::disk('public')->exists($samplePdfPath)) {
            // Simple 1-page valid PDF header bytes
            $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000052 00000 n \n0000000101 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";
            Storage::disk('public')->put($samplePdfPath, $pdfContent);
        }

        $dokumenList = [
            // ── 1. INFORMASI BERKALA ─────────────────────────────────────────────────────────────
            [
                'judul' => 'Rencana Kerja Pemerintah Daerah (RKPD) Kabupaten Ciamis Tahun 2025',
                'kategori_informasi_publik_id' => $katBerkala ? $katBerkala->id : 1,
                'jenis_dokumen' => 'RKPD',
                'tahun' => 2025,
                'ringkasan' => 'Dokumen rencana pembangunan tahunan Kabupaten Ciamis tahun 2025 yang memuat arah kebijakan prioritas daerah, sasaran makro, dan alokasi program lintas perangkat daerah.',
                'penanggung_jawab' => 'Bidang Perencanaan, Pengendalian & Evaluasi Pembangunan',
                'unit_pengolah_id' => $unitPerencanaan?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '8.4 MB',
                'tipe_media' => 'PDF',
                'download_count' => 142,
            ],
            [
                'judul' => 'Rencana Kerja (Renja) Bapperida Kabupaten Ciamis Tahun 2025',
                'kategori_informasi_publik_id' => $katBerkala ? $katBerkala->id : 1,
                'jenis_dokumen' => 'Renja',
                'tahun' => 2025,
                'ringkasan' => 'Dokumen perencanaan tahunan internal Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah (Bapperida) Ciamis untuk tahun anggaran 2025.',
                'penanggung_jawab' => 'Sekretariat Bapperida',
                'unit_pengolah_id' => $unitSekretariat?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '4.2 MB',
                'tipe_media' => 'PDF',
                'download_count' => 87,
            ],
            [
                'judul' => 'Laporan Kinerja Instansi Pemerintah (LKjIP) Bapperida Kabupaten Ciamis Tahun 2024',
                'kategori_informasi_publik_id' => $katBerkala ? $katBerkala->id : 1,
                'jenis_dokumen' => 'Laporan Kinerja',
                'tahun' => 2024,
                'ringkasan' => 'Laporan akuntabilitas kinerja instansi pemerintah yang memuat capaian indikator kinerja utama (IKU) dan sasaran strategis Bapperida Ciamis selama TA 2024.',
                'penanggung_jawab' => 'Subbag Penyusunan Program & Akuntabilitas',
                'unit_pengolah_id' => $unitSekretariat?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '5.1 MB',
                'tipe_media' => 'PDF',
                'download_count' => 64,
            ],
            [
                'judul' => 'Laporan Realisasi Anggaran & Operasional Keuangan Bapperida TA 2024',
                'kategori_informasi_publik_id' => $katBerkala ? $katBerkala->id : 1,
                'jenis_dokumen' => 'Laporan Keuangan',
                'tahun' => 2024,
                'ringkasan' => 'Publikasi transparansi keuangan dan realisasi penyerapan anggaran program/kegiatan Bapperida Ciamis per semester dan tahunan.',
                'penanggung_jawab' => 'Subbag Keuangan & Aset Bapperida',
                'unit_pengolah_id' => $unitSekretariat?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '3.8 MB',
                'tipe_media' => 'PDF',
                'download_count' => 53,
            ],
            [
                'judul' => 'Profil Lengkap, Tugas Fungsi & Struktur Organisasi Bapperida Ciamis 2025',
                'kategori_informasi_publik_id' => $katBerkala ? $katBerkala->id : 1,
                'jenis_dokumen' => 'Profil Lembaga',
                'tahun' => 2025,
                'ringkasan' => 'Informasi komprehensif mengenai profil kelembagaan, bagan struktur organisasi, tugas pokok dan fungsi, serta Daftar Urut Kepangkatan (DUK) pegawai Bapperida.',
                'penanggung_jawab' => 'Subbag Umum & Kepegawaian',
                'unit_pengolah_id' => $unitSekretariat?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '2.9 MB',
                'tipe_media' => 'PDF',
                'download_count' => 110,
            ],

            // ── 2. INFORMASI SETIAP SAAT ──────────────────────────────────────────────────────────
            [
                'judul' => 'Rencana Pembangunan Jangka Panjang Daerah (RPJPD) Kabupaten Ciamis 2025-2045',
                'kategori_informasi_publik_id' => $katSetiapSaat ? $katSetiapSaat->id : 3,
                'jenis_dokumen' => 'RPJPD',
                'tahun' => 2025,
                'ringkasan' => 'Dokumen induk perencanaan pembangunan 20 tahun Kabupaten Ciamis menuju visi daerah maju, berkelanjutan, dan berdaya saing tahun 2045.',
                'penanggung_jawab' => 'Bidang Perencanaan, Pengendalian & Evaluasi Pembangunan',
                'unit_pengolah_id' => $unitPerencanaan?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '14.6 MB',
                'tipe_media' => 'PDF',
                'download_count' => 312,
            ],
            [
                'judul' => 'Rencana Pembangunan Jangka Menengah Daerah (RPJMD) Kabupaten Ciamis 2021-2026',
                'kategori_informasi_publik_id' => $katSetiapSaat ? $katSetiapSaat->id : 3,
                'jenis_dokumen' => 'RPJMD',
                'tahun' => 2021,
                'ringkasan' => 'Penjabaran visi, misi, dan program prioritas kepala daerah untuk periode 5 tahun yang menjadi pedoman seluruh Renstra OPD di lingkungan Pemkab Ciamis.',
                'penanggung_jawab' => 'Bidang Perencanaan, Pengendalian & Evaluasi Pembangunan',
                'unit_pengolah_id' => $unitPerencanaan?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '18.2 MB',
                'tipe_media' => 'PDF',
                'download_count' => 278,
            ],
            [
                'judul' => 'Rencana Strategis (Renstra) Bapperida Kabupaten Ciamis 2021-2026',
                'kategori_informasi_publik_id' => $katSetiapSaat ? $katSetiapSaat->id : 3,
                'jenis_dokumen' => 'Renstra',
                'tahun' => 2024,
                'ringkasan' => 'Dokumen perencanaan 5 tahunan Bapperida yang memuat tujuan, sasaran, strategi, dan indikator program perencanaan, riset, dan inovasi daerah.',
                'penanggung_jawab' => 'Sekretariat Bapperida',
                'unit_pengolah_id' => $unitSekretariat?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '6.3 MB',
                'tipe_media' => 'PDF',
                'download_count' => 119,
            ],
            [
                'judul' => 'Rencana Tata Ruang Wilayah (RTRW) Kabupaten Ciamis 2023-2043 (Perda No. 3/2023)',
                'kategori_informasi_publik_id' => $katSetiapSaat ? $katSetiapSaat->id : 3,
                'jenis_dokumen' => 'RTRW',
                'tahun' => 2023,
                'ringkasan' => 'Peraturan Daerah dan lampiran peta struktur ruang, pola ruang, kawasan lindung, kawasan budidaya, dan ketentuan umum zonasi Kabupaten Ciamis.',
                'penanggung_jawab' => 'Bidang Infrastruktur, Kewilayahan & Tata Ruang',
                'unit_pengolah_id' => $unitInfrastruktur?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '24.5 MB',
                'tipe_media' => 'PDF',
                'download_count' => 460,
            ],
            [
                'judul' => 'Rencana Detail Tata Ruang (RDTR) Kawasan Perkotaan Ciamis',
                'kategori_informasi_publik_id' => $katSetiapSaat ? $katSetiapSaat->id : 3,
                'jenis_dokumen' => 'RDTR',
                'tahun' => 2024,
                'ringkasan' => 'Dokumen rencana rinci tata ruang kawasan perkotaan Ciamis sebagai dasar konfirmasi kesesuaian kegiatan pemanfaatan ruang (KKPR) dan perizinan berusaha.',
                'penanggung_jawab' => 'Bidang Infrastruktur, Kewilayahan & Tata Ruang',
                'unit_pengolah_id' => $unitInfrastruktur?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '12.1 MB',
                'tipe_media' => 'PDF',
                'download_count' => 205,
            ],
            [
                'judul' => 'Hasil Riset & Kajian Makroekonomi serta Penanggulangan Kemiskinan Daerah Ciamis',
                'kategori_informasi_publik_id' => $katSetiapSaat ? $katSetiapSaat->id : 3,
                'jenis_dokumen' => 'Kajian & Riset',
                'tahun' => 2024,
                'ringkasan' => 'Publikasi hasil penelitian ekonomi makro, tren inflasi daerah, disparitas kewilayahan, dan rekomendasi intervensi pengentasan kemiskinan ekstrem.',
                'penanggung_jawab' => 'Bidang Riset dan Inovasi Daerah (Litbang)',
                'unit_pengolah_id' => $unitLitbang?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '7.5 MB',
                'tipe_media' => 'PDF',
                'download_count' => 193,
            ],
            [
                'judul' => 'Dokumen Roadmap Sistem Inovasi Daerah (SIDa) Kabupaten Ciamis',
                'kategori_informasi_publik_id' => $katSetiapSaat ? $katSetiapSaat->id : 3,
                'jenis_dokumen' => 'Roadmap SIDa',
                'tahun' => 2024,
                'ringkasan' => 'Peta jalan ekosistem inovasi daerah untuk penguatan teknologi pertanian, transformasi digital layanan publik, dan pengembangan UMKM unggulan daerah.',
                'penanggung_jawab' => 'Bidang Riset dan Inovasi Daerah (Litbang)',
                'unit_pengolah_id' => $unitLitbang?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '6.7 MB',
                'tipe_media' => 'PDF',
                'download_count' => 95,
            ],
            [
                'judul' => 'Laporan Hasil Evaluasi Pelaksanaan RKPD Kabupaten Ciamis Triwulan IV 2024',
                'kategori_informasi_publik_id' => $katSetiapSaat ? $katSetiapSaat->id : 3,
                'jenis_dokumen' => 'Evaluasi Pembangunan',
                'tahun' => 2024,
                'ringkasan' => 'Evaluasi capaian realisasi fisik dan keuangan serta target indikator kinerja program prioritas pembangunan daerah tahun 2024.',
                'penanggung_jawab' => 'Bidang Perencanaan, Pengendalian & Evaluasi Pembangunan',
                'unit_pengolah_id' => $unitPerencanaan?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '5.9 MB',
                'tipe_media' => 'PDF',
                'download_count' => 77,
            ],
            [
                'judul' => 'Dokumen Hasil Musyawarah Perencanaan Pembangunan (Musrenbang) RKPD 2025',
                'kategori_informasi_publik_id' => $katSetiapSaat ? $katSetiapSaat->id : 3,
                'jenis_dokumen' => 'Musrenbang',
                'tahun' => 2024,
                'ringkasan' => 'Kompilasi usulan aspirasi masyarakat hasil musrenbang desa/kelurahan, musrenbang kecamatan, dan forum lintas perangkat daerah tahun 2024.',
                'penanggung_jawab' => 'Bidang Perencanaan, Pengendalian & Evaluasi Pembangunan',
                'unit_pengolah_id' => $unitPerencanaan?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '9.2 MB',
                'tipe_media' => 'PDF',
                'download_count' => 134,
            ],
            [
                'judul' => 'Daftar Aset, Inventaris Barang Milik Daerah & Fasilitas Publik Bapperida Ciamis 2024',
                'kategori_informasi_publik_id' => $katSetiapSaat ? $katSetiapSaat->id : 3,
                'jenis_dokumen' => 'Daftar Aset',
                'tahun' => 2024,
                'ringkasan' => 'Daftar inventarisasi sarana, prasarana, gedung kantor, kendaraan dinas operasional, dan peralatan kantor di lingkungan Bapperida Ciamis.',
                'penanggung_jawab' => 'Subbag Umum & Kepegawaian',
                'unit_pengolah_id' => $unitSekretariat?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '2.1 MB',
                'tipe_media' => 'PDF',
                'download_count' => 45,
            ],

            // ── 3. INFORMASI SERTA MERTA ──────────────────────────────────────────────────────────
            [
                'judul' => 'Peta Kawasan Kerentanan Bencana & Mitigasi Tata Ruang Wilayah Kabupaten Ciamis',
                'kategori_informasi_publik_id' => $katSertaMerta ? $katSertaMerta->id : 2,
                'jenis_dokumen' => 'RTRW',
                'tahun' => 2024,
                'ringkasan' => 'Informasi peringatan dini tata ruang mengenai kawasan sempadan sungai rawan banjir, kawasan perbukitan rawan longsor, serta panduan mitigasi evakuasi warga.',
                'penanggung_jawab' => 'Bidang Infrastruktur, Kewilayahan & Tata Ruang',
                'unit_pengolah_id' => $unitInfrastruktur?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '11.3 MB',
                'tipe_media' => 'PDF',
                'download_count' => 218,
            ],
            [
                'judul' => 'Prosedur & Protokol Darurat Penataan Ruang Terkait Tanggap Bencana Ciamis',
                'kategori_informasi_publik_id' => $katSertaMerta ? $katSertaMerta->id : 2,
                'jenis_dokumen' => 'Lainnya',
                'tahun' => 2024,
                'ringkasan' => 'Protokol kesiapsiagaan badan publik dalam penyesuaian ruang publik dan alokasi infrastruktur darurat pada masa tanggap darurat bencana alam di wilayah Ciamis.',
                'penanggung_jawab' => 'Bapperida Kabupaten Ciamis',
                'unit_pengolah_id' => $unitInfrastruktur?->id,
                'file_path' => $samplePdfPath,
                'file_size' => '3.4 MB',
                'tipe_media' => 'PDF',
                'download_count' => 88,
            ],
        ];

        foreach ($dokumenList as $item) {
            InformasiPublik::updateOrCreate(
                ['judul' => $item['judul']],
                $item
            );
        }
    }
}
