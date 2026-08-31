<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CaraMemperolehInformasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_cara' => 'Melihat langsung / Membaca / Mendengarkan / Mencatat', 'deskripsi' => 'Datang langsung ke kantor Bappeda'],
            ['nama_cara' => 'Email (Softcopy)', 'deskripsi' => 'Dokumen akan dikirimkan ke alamat email terdaftar'],
            ['nama_cara' => 'Salinan Cetak (Hardcopy)', 'deskripsi' => 'Dapat diambil atau dikirim (biaya pengiriman ditanggung pemohon)'],
        ];

        foreach ($data as $item) {
            \App\Models\CaraMemperolehInformasi::updateOrCreate(['nama_cara' => $item['nama_cara']], $item);
        }
    }
}
