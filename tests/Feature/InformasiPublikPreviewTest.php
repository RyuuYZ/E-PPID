<?php

namespace Tests\Feature;

use App\Models\InformasiPublik;
use App\Models\KategoriInformasiPublik;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InformasiPublikPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['deskripsi' => 'Super Administrator']);
        $this->adminUser = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->kategori = KategoriInformasiPublik::create([
            'nama_kategori' => 'Informasi Berkala',
            'deskripsi' => 'Informasi yang diperbarui secara berkala',
            'icon' => 'schedule',
        ]);
    }

    public function test_landing_page_shows_dip_section_and_preview_modal()
    {
        InformasiPublik::create([
            'judul' => 'RKPD Ciamis 2025 Test',
            'kategori_informasi_publik_id' => $this->kategori->id,
            'jenis_dokumen' => 'RKPD',
            'tahun' => 2025,
            'ringkasan' => 'Ringkasan dokumen RKPD 2025.',
            'penanggung_jawab' => 'Bapperida Ciamis',
            'file_size' => '2.5 MB',
            'tipe_media' => 'PDF',
            'is_active' => true,
            'download_count' => 50,
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('RKPD Ciamis 2025 Test');
        $response->assertSee('Preview');
        $response->assertSee('modal-preview-title-home');
    }

    public function test_public_dip_page_shows_preview_button_and_modal()
    {
        $dokumen = InformasiPublik::create([
            'judul' => 'RPJMD Ciamis 2025-2029 Test',
            'kategori_informasi_publik_id' => $this->kategori->id,
            'jenis_dokumen' => 'RPJMD',
            'tahun' => 2025,
            'ringkasan' => 'Rencana jangka menengah 5 tahun.',
            'penanggung_jawab' => 'Bapperida Ciamis',
            'file_size' => '5.1 MB',
            'tipe_media' => 'PDF',
            'is_active' => true,
            'download_count' => 10,
        ]);

        $response = $this->get(route('informasi-publik.index'));
        $response->assertStatus(200);
        $response->assertSee('RPJMD Ciamis 2025-2029 Test');
        $response->assertSee('Preview');
        $response->assertSee('modal-preview-title');
    }

    public function test_download_with_inline_query_streams_pdf_for_preview()
    {
        $dokumen = InformasiPublik::create([
            'judul' => 'Kajian Makroekonomi Ciamis',
            'kategori_informasi_publik_id' => $this->kategori->id,
            'jenis_dokumen' => 'Kajian & Riset',
            'tahun' => 2024,
            'ringkasan' => 'Kajian pertumbuhan ekonomi daerah.',
            'penanggung_jawab' => 'Bidang Litbang',
            'file_size' => '1.2 MB',
            'tipe_media' => 'PDF',
            'is_active' => true,
            'download_count' => 5,
        ]);

        $initialDownloads = $dokumen->download_count;

        $response = $this->get(route('informasi-publik.download', ['id' => $dokumen->id, 'inline' => 1]));
        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));

        // Inline preview must not increment download count
        $this->assertEquals($initialDownloads, $dokumen->fresh()->download_count);
    }

    public function test_admin_can_preview_inactive_draft_documents()
    {
        $draft = InformasiPublik::create([
            'judul' => 'Draf Rencana Inovasi Daerah',
            'kategori_informasi_publik_id' => $this->kategori->id,
            'jenis_dokumen' => 'Roadmap SIDa',
            'tahun' => 2025,
            'ringkasan' => 'Draf internal Bapperida belum dipublikasi.',
            'penanggung_jawab' => 'Bidang Inovasi',
            'file_size' => '3.0 MB',
            'tipe_media' => 'PDF',
            'is_active' => false,
            'download_count' => 0,
        ]);

        // Public user should get 404
        $publicResponse = $this->get(route('informasi-publik.download', ['id' => $draft->id, 'inline' => 1]));
        $publicResponse->assertStatus(404);

        // Authenticated admin should get 200 stream
        $adminResponse = $this->actingAs($this->adminUser)->get(route('informasi-publik.download', ['id' => $draft->id, 'inline' => 1]));
        $adminResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $adminResponse->headers->get('content-type'));
    }

    public function test_admin_dip_index_shows_preview_button_and_modal()
    {
        InformasiPublik::create([
            'judul' => 'Dokumen Evaluasi Kinerja Bapperida',
            'kategori_informasi_publik_id' => $this->kategori->id,
            'jenis_dokumen' => 'Evaluasi Pembangunan',
            'tahun' => 2024,
            'ringkasan' => 'Laporan capaian indikator kinerja.',
            'penanggung_jawab' => 'Bapperida Ciamis',
            'file_size' => '2.0 MB',
            'tipe_media' => 'PDF',
            'is_active' => true,
            'download_count' => 12,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.informasi-publik.index'));
        $response->assertStatus(200);
        $response->assertSee('Dokumen Evaluasi Kinerja Bapperida');
        $response->assertSee('Preview Dokumen PDF');
        $response->assertSee('modal-title');
    }
}
