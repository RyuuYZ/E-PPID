<?php

namespace Tests\Feature;

use App\Models\CaraMemperolehInformasi;
use App\Models\KategoriPemohon;
use App\Services\CaptchaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CaptchaSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $kategoriPemohon;
    protected $caraMemperoleh;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kategoriPemohon = KategoriPemohon::create(['nama_kategori' => 'Perorangan']);
        $this->caraMemperoleh = CaraMemperolehInformasi::create(['nama_cara' => 'Melihat / Membaca']);
    }

    public function test_permohonan_page_renders_with_turnstile_widget()
    {
        $response = $this->get(route('permohonan.create'));
        $response->assertStatus(200);
        $response->assertSee('Verifikasi Keamanan');
        $response->assertSee('cf-turnstile');
        $response->assertSee('challenges.cloudflare.com/turnstile/v0/api.js');
    }

    public function test_blocks_submission_without_turnstile_response()
    {
        Storage::fake('local');
        Mail::fake();

        $validFile = UploadedFile::fake()->image('ktp.jpg', 600, 400);

        $response = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Warga Test',
            'kategori_pemohon_id' => $this->kategoriPemohon->id,
            'nik_atau_no_badan_hukum' => '3207011204950001',
            'no_telp' => '08123456789',
            'email' => 'warga@example.com',
            'alamat' => 'Jl. Ciamis No. 10',
            'subjek_informasi' => 'Permintaan Data',
            'rincian_informasi' => 'Rincian data',
            'tujuan_penggunaan' => 'Riset',
            'cara_memperoleh_informasi_id' => $this->caraMemperoleh->id,
            'file_identitas' => $validFile,
            // Missing cf-turnstile-response
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertDatabaseMissing('permohonan_informasis', [
            'email' => 'warga@example.com',
        ]);
    }

    public function test_blocks_submission_with_invalid_turnstile_token()
    {
        Storage::fake('local');
        Mail::fake();

        $validFile = UploadedFile::fake()->image('ktp.jpg', 600, 400);

        $response = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Warga Test',
            'kategori_pemohon_id' => $this->kategoriPemohon->id,
            'nik_atau_no_badan_hukum' => '3207011204950001',
            'no_telp' => '08123456789',
            'email' => 'warga@example.com',
            'alamat' => 'Jl. Ciamis No. 10',
            'subjek_informasi' => 'Permintaan Data',
            'rincian_informasi' => 'Rincian data',
            'tujuan_penggunaan' => 'Riset',
            'cara_memperoleh_informasi_id' => $this->caraMemperoleh->id,
            'file_identitas' => $validFile,
            'cf-turnstile-response' => 'invalid-token',
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertDatabaseMissing('permohonan_informasis', [
            'email' => 'warga@example.com',
        ]);
    }

    public function test_blocks_submission_when_honeypot_is_filled()
    {
        Storage::fake('local');
        Mail::fake();

        $validFile = UploadedFile::fake()->image('ktp.jpg', 600, 400);

        $response = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Spam Bot',
            'kategori_pemohon_id' => $this->kategoriPemohon->id,
            'nik_atau_no_badan_hukum' => '3207011204950001',
            'no_telp' => '08123456789',
            'email' => 'bot@example.com',
            'alamat' => 'Jl. Spam No. 1',
            'subjek_informasi' => 'Spam Subject',
            'rincian_informasi' => 'Spam Content',
            'tujuan_penggunaan' => 'Spamming',
            'cara_memperoleh_informasi_id' => $this->caraMemperoleh->id,
            'file_identitas' => $validFile,
            'cf-turnstile-response' => 'valid-dummy-turnstile-token',
            '_hp_website' => 'http://spam-link.com', // Honeypot filled by bot
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertDatabaseMissing('permohonan_informasis', [
            'email' => 'bot@example.com',
        ]);
    }

    public function test_allows_submission_with_turnstile_response_and_valid_file()
    {
        Storage::fake('local');
        Mail::fake();

        $validFile = UploadedFile::fake()->image('ktp_resmi.jpg', 600, 400);

        $response = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Budi Santoso',
            'kategori_pemohon_id' => $this->kategoriPemohon->id,
            'nik_atau_no_badan_hukum' => '3207011204950001',
            'no_telp' => '08123456789',
            'email' => 'budi@example.com',
            'alamat' => 'Jl. Ciamis No. 10',
            'subjek_informasi' => 'Permintaan Rencana Tata Ruang',
            'rincian_informasi' => 'Dokumen RTRW Ciamis 2024',
            'tujuan_penggunaan' => 'Penelitian Akademis',
            'cara_memperoleh_informasi_id' => $this->caraMemperoleh->id,
            'file_identitas' => $validFile,
            'cf-turnstile-response' => 'valid-dummy-turnstile-token',
            '_hp_website' => '',
        ]);

        $response->assertRedirect(route('permohonan.sukses'));
        $this->assertDatabaseHas('permohonan_informasis', [
            'email' => 'budi@example.com',
        ]);
    }
}
