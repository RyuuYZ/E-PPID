<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriInformasiPublikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_kategori' => 'Informasi Berkala',
                'deskripsi' => 'Informasi yang wajib disediakan dan diumumkan secara berkala, minimal 6 bulan sekali.',
                'icon' => 'calendar_month',
                'bg_color' => 'bg-primary-fixed',
                'text_color' => 'text-primary-container'
            ],
            [
                'nama_kategori' => 'Serta Merta',
                'deskripsi' => 'Informasi publik yang dapat mengancam hajat hidup orang banyak dan ketertiban umum.',
                'icon' => 'campaign',
                'bg_color' => 'bg-secondary-fixed',
                'text_color' => 'text-on-secondary-fixed'
            ],
            [
                'nama_kategori' => 'Setiap Saat',
                'deskripsi' => 'Informasi yang harus disediakan oleh Badan Publik dan siap tersedia untuk bisa langsung diberikan.',
                'icon' => 'schedule',
                'bg_color' => 'bg-tertiary-fixed',
                'text_color' => 'text-on-tertiary-fixed'
            ],
            [
                'nama_kategori' => 'Keberatan',
                'deskripsi' => 'Prosedur pengajuan keberatan jika layanan informasi tidak sesuai dengan ketentuan perundang-undangan.',
                'icon' => 'gavel',
                'bg_color' => 'bg-error-container',
                'text_color' => 'text-on-error-container'
            ],
        ];

        foreach ($data as $item) {
            \App\Models\KategoriInformasiPublik::updateOrCreate(['nama_kategori' => $item['nama_kategori']], $item);
        }
    }
}
