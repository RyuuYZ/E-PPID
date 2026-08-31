<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriPemohonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_kategori' => 'Perorangan'],
            ['nama_kategori' => 'Kelompok Orang'],
            ['nama_kategori' => 'Badan Hukum'],
        ];

        foreach ($data as $item) {
            \App\Models\KategoriPemohon::updateOrCreate(['nama_kategori' => $item['nama_kategori']], $item);
        }
    }
}
