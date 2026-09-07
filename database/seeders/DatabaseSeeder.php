<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UnitPengolahSeeder::class,
            KlasifikasiArsipSeeder::class,
        ]);

        $roles = [
            [
                'name' => 'Atasan PPID Pelaksana',
                'description' => 'Menerima eskalasi & keberatan informasi; menandatangani surat jawaban final untuk permohonan yang sensitif/kompleks; melihat dashboard evaluasi kinerja layanan (jumlah permohonan, rata-rata waktu selesai, tren keberatan); menetapkan kebijakan/arahan layanan'
            ],
            [
                'name' => 'PPID Pelaksana / Pembantu Bappeda',
                'description' => 'Mengoordinasikan permintaan data ke Petugas Penghubung di tiap bidang; menguji/memvalidasi kesesuaian data yang masuk sebelum dijawab ke pemohon; menyusun konsep surat jawaban; mengelola penyimpanan/pengarsipan dokumen perencanaan'
            ],
            [
                'name' => 'Petugas Pelayanan Informasi / Desk Layanan',
                'description' => 'Menerima & mencatat permohonan masuk (dari portal/surat/email); memverifikasi kelengkapan formulir & dokumen pemohon; mengirim notifikasi/Surat Keterangan Tidak Lengkap; mendistribusikan permohonan ke PPID Pelaksana; menyampaikan jawaban final ke pemohon'
            ],
            [
                'name' => 'Petugas Penghubung (Unit Pengolah Data)',
                'description' => 'Menerima notifikasi permintaan data dari PPID Pelaksana; mengunggah/menyediakan data teknis sesuai bidangnya; menandai jika data yang diminta termasuk kategori dikecualikan'
            ],
            [
                'name' => 'Super Admin',
                'description' => 'Administrator sistem dengan hak akses penuh, termasuk pengelolaan master data dan konfigurasi sistem.'
            ],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }

        $users = [
            ['name' => 'Sekretaris (Atasan PPID)', 'email' => 'atasan@bappeda.go.id', 'role' => 'Atasan PPID Pelaksana'],
            ['name' => 'Koordinator (PPID Pelaksana)', 'email' => 'ppid@bappeda.go.id', 'role' => 'PPID Pelaksana / Pembantu Bappeda'],
            ['name' => 'Staf Humas (Desk Layanan)', 'email' => 'desk@bappeda.go.id', 'role' => 'Petugas Pelayanan Informasi / Desk Layanan'],
            ['name' => 'Staf Bidang (Petugas Penghubung)', 'email' => 'penghubung@bappeda.go.id', 'role' => 'Petugas Penghubung (Unit Pengolah Data)'],
            ['name' => 'Super Administrator', 'email' => 'superadmin@bappeda.go.id', 'role' => 'Super Admin'],
        ];

        foreach ($users as $u) {
            $roleId = \App\Models\Role::where('name', $u['role'])->first()->id;
            \App\Models\User::firstOrCreate(['email' => $u['email']], [
                'name' => $u['name'],
                'password' => bcrypt('password'),
                'role_id' => $roleId,
            ]);
        }

        $this->call([
            KategoriPemohonSeeder::class,
            CaraMemperolehInformasiSeeder::class,
            KategoriInformasiPublikSeeder::class,
            PermohonanSeeder::class,
            InformasiPublikSeeder::class,
        ]);
    }
}
