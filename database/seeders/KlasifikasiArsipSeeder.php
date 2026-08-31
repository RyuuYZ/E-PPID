<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\KlasifikasiArsip;

class KlasifikasiArsipSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => '000', 'nama_klasifikasi' => 'Umum', 'keterangan' => 'Urusan Umum, Kesekretariatan'],
            ['kode' => '100', 'nama_klasifikasi' => 'Pemerintahan', 'keterangan' => 'Urusan Pemerintahan Daerah'],
            ['kode' => '800', 'nama_klasifikasi' => 'Kepegawaian', 'keterangan' => 'Urusan Kepegawaian dan SDM'],
            ['kode' => '900', 'nama_klasifikasi' => 'Keuangan', 'keterangan' => 'Urusan Keuangan, Anggaran, dan Akuntansi'],
        ];

        foreach ($data as $item) {
            KlasifikasiArsip::firstOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
