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
        $roles = [
            ['name' => 'Atasan PPID Pelaksana', 'description' => 'Menerima eskalasi, tandatangan surat, menetapkan kebijakan'],
            ['name' => 'PPID Pelaksana', 'description' => 'Koordinator data, uji/validasi jawaban, susun konsep'],
            ['name' => 'Desk Layanan', 'description' => 'Penerima permohonan, verifikasi kelengkapan, kirim jawaban'],
            ['name' => 'Petugas Penghubung', 'description' => 'Unit pengolah data per bidang'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::firstOrCreate(['name' => $role['name']], $role);
        }

        $users = [
            ['name' => 'Sekretaris (Atasan PPID)', 'email' => 'atasan@bappeda.go.id', 'role' => 'Atasan PPID Pelaksana'],
            ['name' => 'Koordinator (PPID Pelaksana)', 'email' => 'ppid@bappeda.go.id', 'role' => 'PPID Pelaksana'],
            ['name' => 'Staf Humas (Desk Layanan)', 'email' => 'desk@bappeda.go.id', 'role' => 'Desk Layanan'],
            ['name' => 'Staf Bidang (Petugas Penghubung)', 'email' => 'penghubung@bappeda.go.id', 'role' => 'Petugas Penghubung'],
        ];

        foreach ($users as $u) {
            $roleId = \App\Models\Role::where('name', $u['role'])->first()->id;
            \App\Models\User::firstOrCreate(['email' => $u['email']], [
                'name' => $u['name'],
                'password' => bcrypt('password'),
                'role_id' => $roleId,
            ]);
        }
    }
}
