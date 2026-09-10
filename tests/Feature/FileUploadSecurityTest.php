<?php

namespace Tests\Feature;

use App\Models\CaraMemperolehInformasi;
use App\Models\KategoriInformasiPublik;
use App\Models\KategoriPemohon;
use App\Models\Role;
use App\Models\User;
use App\Rules\SecureFile;
use App\Services\FileSecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $kategoriPemohon;
    protected $caraMemperoleh;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['deskripsi' => 'Super Administrator']);
        $this->adminUser = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->kategoriPemohon = KategoriPemohon::create(['nama_kategori' => 'Perorangan']);
        $this->caraMemperoleh = CaraMemperolehInformasi::create(['nama_cara' => 'Melihat / Membaca']);
    }

    public function test_blocks_php_script_disguised_as_jpg()
    {
        Storage::fake('local');
        Mail::fake();

        // Malicious file: plain PHP script saved with .jpg extension
        $maliciousFile = UploadedFile::fake()->createWithContent(
            'exploit.jpg',
            "<?php\necho 'Hacked!';\nsystem(\$_GET['cmd']);\n?>"
        );

        $response = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Hacker Test',
            'kategori_pemohon_id' => $this->kategoriPemohon->id,
            'nik_atau_no_badan_hukum' => '3207011204950001',
            'no_telp' => '08123456789',
            'email' => 'hacker@example.com',
            'alamat' => 'Jl. Test No. 1',
            'subjek_informasi' => 'Test Subject',
            'rincian_informasi' => 'Test Rincian',
            'tujuan_penggunaan' => 'Test Tujuan',
            'cara_memperoleh_informasi_id' => $this->caraMemperoleh->id,
            'file_identitas' => $maliciousFile,
        ]);

        $response->assertSessionHasErrors('file_identitas');
        $this->assertDatabaseMissing('permohonan_informasis', [
            'email' => 'hacker@example.com',
        ]);
    }

    public function test_blocks_polyglot_jpeg_containing_php_code()
    {
        Storage::fake('local');
        Mail::fake();

        // Polyglot file: starts with JPEG magic bytes FF D8 FF but contains embedded PHP shell
        $polyglotContent = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00`\x00`\x00\x00\xFF\xDB\x00C\x00\n<?php phpinfo(); system(\$_GET['c']); ?>\xFF\xD9";
        $polyglotFile = UploadedFile::fake()->createWithContent('polyglot.jpg', $polyglotContent);

        $response = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Hacker Polyglot',
            'kategori_pemohon_id' => $this->kategoriPemohon->id,
            'nik_atau_no_badan_hukum' => '3207011204950001',
            'no_telp' => '08123456789',
            'email' => 'polyglot@example.com',
            'alamat' => 'Jl. Test No. 2',
            'subjek_informasi' => 'Test Polyglot',
            'rincian_informasi' => 'Test Rincian',
            'tujuan_penggunaan' => 'Test Tujuan',
            'cara_memperoleh_informasi_id' => $this->caraMemperoleh->id,
            'file_identitas' => $polyglotFile,
        ]);

        $response->assertSessionHasErrors('file_identitas');
        $this->assertDatabaseMissing('permohonan_informasis', [
            'email' => 'polyglot@example.com',
        ]);
    }

    public function test_blocks_double_extension_files()
    {
        Storage::fake('local');
        Mail::fake();

        // Double extension: exploit.php.jpg or backdoor.phtml.png
        $doubleExtFile = UploadedFile::fake()->createWithContent('backdoor.php.jpg', "\xFF\xD8\xFF\xE0some content\xFF\xD9");

        $response = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Hacker DoubleExt',
            'kategori_pemohon_id' => $this->kategoriPemohon->id,
            'nik_atau_no_badan_hukum' => '3207011204950001',
            'no_telp' => '08123456789',
            'email' => 'doubleext@example.com',
            'alamat' => 'Jl. Test No. 3',
            'subjek_informasi' => 'Test Double Ext',
            'rincian_informasi' => 'Test Rincian',
            'tujuan_penggunaan' => 'Test Tujuan',
            'cara_memperoleh_informasi_id' => $this->caraMemperoleh->id,
            'file_identitas' => $doubleExtFile,
        ]);

        $response->assertSessionHasErrors('file_identitas');
    }

    public function test_blocks_html_javascript_xss_file_disguised_as_png()
    {
        Storage::fake('local');
        Mail::fake();

        $xssFile = UploadedFile::fake()->createWithContent(
            'avatar.png',
            "<script>alert('XSS Stealing Cookie: ' + document.cookie);</script>"
        );

        $response = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Hacker XSS',
            'kategori_pemohon_id' => $this->kategoriPemohon->id,
            'nik_atau_no_badan_hukum' => '3207011204950001',
            'no_telp' => '08123456789',
            'email' => 'xss@example.com',
            'alamat' => 'Jl. Test No. 4',
            'subjek_informasi' => 'Test XSS',
            'rincian_informasi' => 'Test Rincian',
            'tujuan_penggunaan' => 'Test Tujuan',
            'cara_memperoleh_informasi_id' => $this->caraMemperoleh->id,
            'file_identitas' => $xssFile,
        ]);

        $response->assertSessionHasErrors('file_identitas');
    }

    public function test_blocks_executable_binary_disguised_as_pdf()
    {
        Storage::fake('public');

        $kat = KategoriInformasiPublik::create([
            'nama_kategori' => 'Informasi Berkala',
            'deskripsi' => 'Informasi berkala',
        ]);

        // Windows PE Executable disguised as .pdf
        $exeFile = UploadedFile::fake()->createWithContent('malware.pdf', "MZ\x90\x00\x03\x00\x00\x00\x04\x00\x00\x00\xFF\xFF\x00\x00");

        $response = $this->actingAs($this->adminUser)->post(route('admin.informasi-publik.store'), [
            'judul' => 'Malicious File Document',
            'kategori_informasi_publik_id' => $kat->id,
            'jenis_dokumen' => 'RKPD',
            'tahun' => 2025,
            'file_dokumen' => $exeFile,
        ]);

        $response->assertSessionHasErrors('file_dokumen');
    }

    public function test_allows_valid_legitimate_jpg_image()
    {
        Storage::fake('local');
        Mail::fake();

        // Valid image generated with real GD image generator
        $validImage = UploadedFile::fake()->image('ktp_asli.jpg', 600, 400);

        $response = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Warga Asli',
            'kategori_pemohon_id' => $this->kategoriPemohon->id,
            'nik_atau_no_badan_hukum' => '3207011204950001',
            'no_telp' => '08123456789',
            'email' => 'warga@example.com',
            'alamat' => 'Jl. Ciamis No. 1',
            'subjek_informasi' => 'Informasi Tata Ruang',
            'rincian_informasi' => 'Rincian RTRW',
            'tujuan_penggunaan' => 'Penelitian',
            'cara_memperoleh_informasi_id' => $this->caraMemperoleh->id,
            'file_identitas' => $validImage,
        ]);

        $response->assertRedirect(route('permohonan.sukses'));
        $this->assertDatabaseHas('permohonan_informasis', [
            'email' => 'warga@example.com',
        ]);
    }

    public function test_allows_valid_legitimate_pdf_document()
    {
        Storage::fake('local');
        Mail::fake();

        $validPdf = UploadedFile::fake()->createWithContent(
            'identitas_resmi.pdf',
            "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000052 00000 n \n0000000101 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF"
        );

        $response = $this->post(route('permohonan.store'), [
            'nama_pemohon' => 'Warga PDF Asli',
            'kategori_pemohon_id' => $this->kategoriPemohon->id,
            'nik_atau_no_badan_hukum' => '3207011204950001',
            'no_telp' => '08123456789',
            'email' => 'warga_pdf@example.com',
            'alamat' => 'Jl. Ciamis No. 2',
            'subjek_informasi' => 'Informasi Perencanaan',
            'rincian_informasi' => 'Rincian RPJMD',
            'tujuan_penggunaan' => 'Kajian',
            'cara_memperoleh_informasi_id' => $this->caraMemperoleh->id,
            'file_identitas' => $validPdf,
        ]);

        $response->assertRedirect(route('permohonan.sukses'));
        $this->assertDatabaseHas('permohonan_informasis', [
            'email' => 'warga_pdf@example.com',
        ]);
    }
}
