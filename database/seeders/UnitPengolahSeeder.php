<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitPengolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bidang = [
            ['nama_bidang' => 'Bidang Infrastruktur & Kewilayahan', 'deskripsi' => 'Mengurus tata ruang dan infrastruktur'],
            ['nama_bidang' => 'Bidang Ekonomi', 'deskripsi' => 'Mengurus perekonomian daerah'],
            ['nama_bidang' => 'Bidang Sosial Budaya', 'deskripsi' => 'Mengurus sosial dan kebudayaan'],
            ['nama_bidang' => 'Sekretariat', 'deskripsi' => 'Administrasi umum dan kepegawaian'],
        ];

        foreach ($bidang as $b) {
            \App\Models\UnitPengolah::create($b);
        }
    }
}
