<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\PermohonanInformasi;
use App\Models\KategoriPemohon;
use App\Models\CaraMemperolehInformasi;

class PermohonanTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['deskripsi' => 'Super Administrator']);
        $this->adminUser = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_can_view_permohonan_index_page()
    {
        $kategori = KategoriPemohon::create(['nama_kategori' => 'Perorangan']);
        $cara = CaraMemperolehInformasi::create(['nama_cara' => 'Melihat / Membaca']);

        PermohonanInformasi::create([
            'nomor_registrasi' => 'REG-2026-TEST001',
            'nama_pemohon' => 'Budi Santoso',
            'nik_atau_no_badan_hukum' => '3207010101010001',
            'no_telp' => '081234567890',
            'email' => 'budi@example.com',
            'alamat' => 'Jl. Ciamis No. 1',
            'pekerjaan' => 'Wiraswasta',
            'kategori_pemohon_id' => $kategori->id,
            'cara_memperoleh_informasi_id' => $cara->id,
            'cara_mendapatkan_salinan' => 'Softcopy',
            'rincian_informasi' => 'Data LPPD Tahun 2025',
            'tujuan_penggunaan' => 'Penelitian Akademik',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.permohonan.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Permohonan Informasi');
        $response->assertSee('REG-2026-TEST001');
        $response->assertSee('Budi Santoso');
    }
}
