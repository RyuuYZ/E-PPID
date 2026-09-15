<?php

namespace Database\Seeders;

use App\Models\CarouselItem;
use Illuminate\Database\Seeder;

class CarouselSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slides = [
            [
                'title' => 'Keterbukaan Informasi Publik Terpadu & Akuntabel',
                'subtitle' => 'Mewujudkan tata kelola pemerintahan yang transparan dan memudahkan masyarakat dalam mengakses dokumen perencanaan daerah Kabupaten Ciamis.',
                'badge_text' => 'Portal Resmi E-PPID',
                'image_path' => 'images/carousels/slide1.svg',
                'button_text' => 'Ajukan Permohonan',
                'button_url' => '/permohonan/baru',
                'button_target' => '_self',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Akses Dokumen Perencanaan Pembangunan Daerah',
                'subtitle' => 'Unduh dan pelajari dokumen RPJPD, RPJMD, RKPD, RTRW, serta kajian strategis Bapperida secara langsung dan cepat.',
                'badge_text' => 'Daftar Informasi Publik',
                'image_path' => 'images/carousels/slide2.svg',
                'button_text' => 'Lihat Dokumen DIP',
                'button_url' => '/informasi-publik',
                'button_target' => '_self',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Pantau Progres Permohonan Informasi Real-Time',
                'subtitle' => 'Lacak status permohonan informasi dan pengajuan keberatan Anda secara transparan kapan saja dengan nomor registrasi unik.',
                'badge_text' => 'Layanan Cepat & Pasti',
                'image_path' => 'images/carousels/slide3.svg',
                'button_text' => 'Lacak Status',
                'button_url' => '/lacak',
                'button_target' => '_self',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($slides as $slide) {
            CarouselItem::firstOrCreate(
                ['title' => $slide['title']],
                $slide
            );
        }
    }
}
