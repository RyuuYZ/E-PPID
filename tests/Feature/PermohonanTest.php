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
        $response->assertSee('Permohonan Informasi');
        $response->assertSee('REG-2026-TEST001');
        $response->assertSee('Budi Santoso');
    }

    public function test_can_submit_permohonan_with_valid_nik_and_separated_subject_and_receives_email()
    {
        \Illuminate\Support\Facades\Mail::fake();
        \Illuminate\Support\Facades\Storage::fake('local');

        $kategori = KategoriPemohon::create(['nama_kategori' => 'Perorangan']);
        $cara = CaraMemperolehInformasi::create(['nama_cara' => 'Melihat / Membaca']);
        $file = \Illuminate\Http\UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf');

        $response = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Ahmad Subagja',
            'kategori_pemohon_id' => $kategori->id,
            'nik_atau_no_badan_hukum' => '3207011204950002',
            'no_telp' => '08123456789',
            'email' => 'ahmad@example.com',
            'kecamatan' => 'Ciamis',
            'desa' => 'Maleber',
            'detail_alamat' => 'Jl. Jend. Sudirman No. 16 RT 01/RW 02',
            'subjek_informasi' => 'Rencana Kerja Bappeda Ciamis 2025',
            'rincian_informasi' => 'Daftar rincian kegiatan perencanaan pembangunan wilayah perkotaan Ciamis.',
            'tujuan_penggunaan' => 'Penyusunan Tesis Magister',
            'cara_memperoleh_informasi_id' => $cara->id,
            'file_identitas' => $file,
        ]);

        $response->assertRedirect(route('permohonan.sukses'));
        $this->assertDatabaseHas('permohonan_informasis', [
            'nama_pemohon' => 'Ahmad Subagja',
            'nik_atau_no_badan_hukum' => '3207011204950002',
            'no_telp' => '+628123456789',
            'subjek_informasi' => 'Rencana Kerja Bappeda Ciamis 2025',
            'rincian_informasi' => 'Daftar rincian kegiatan perencanaan pembangunan wilayah perkotaan Ciamis.',
        ]);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\PermohonanTerkirimMail::class, function ($mail) {
            return $mail->hasTo('ahmad@example.com');
        });

        // Test view sukses
        $saved = PermohonanInformasi::where('email', 'ahmad@example.com')->first();
        $suksesResponse = $this->withSession([
            'nomor_registrasi' => $saved->nomor_registrasi,
            'email' => $saved->email,
        ])->get(route('permohonan.sukses'));

        $suksesResponse->assertStatus(200);
        $suksesResponse->assertSee($saved->nomor_registrasi);
        $suksesResponse->assertSee('ahmad@example.com');
        $suksesResponse->assertSee('Kode Invoice / Registrasi Telah Dikirim ke Email');
    }

    public function test_fails_if_nik_contains_non_digits_or_exceeds_16()
    {
        $kategori = KategoriPemohon::create(['nama_kategori' => 'Perorangan']);
        $cara = CaraMemperolehInformasi::create(['nama_cara' => 'Melihat / Membaca']);
        $file = \Illuminate\Http\UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf');

        // Test > 16 digits
        $responseLong = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Ahmad Subagja',
            'kategori_pemohon_id' => $kategori->id,
            'nik_atau_no_badan_hukum' => '32070112049500029999', // 20 digits
            'no_telp' => '08123456789',
            'email' => 'ahmad@example.com',
            'alamat' => 'Ciamis',
            'subjek_informasi' => 'Rencana Kerja',
            'rincian_informasi' => 'Rincian',
            'tujuan_penggunaan' => 'Tujuan',
            'cara_memperoleh_informasi_id' => $cara->id,
            'file_identitas' => $file,
        ]);
        $responseLong->assertSessionHasErrors('nik_atau_no_badan_hukum');

        // Test non-numeric
        $responseAlpha = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Ahmad Subagja',
            'kategori_pemohon_id' => $kategori->id,
            'nik_atau_no_badan_hukum' => '3207ABCD12345678', // Contains letters
            'no_telp' => '08123456789',
            'email' => 'ahmad@example.com',
            'alamat' => 'Ciamis',
            'subjek_informasi' => 'Rencana Kerja',
            'rincian_informasi' => 'Rincian',
            'tujuan_penggunaan' => 'Tujuan',
            'cara_memperoleh_informasi_id' => $cara->id,
            'file_identitas' => $file,
        ]);
        $responseAlpha->assertSessionHasErrors('nik_atau_no_badan_hukum');
    }
}
