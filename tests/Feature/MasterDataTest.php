<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\UnitPengolah;
use App\Models\KategoriPemohon;
use App\Models\CaraMemperolehInformasi;
use App\Models\KategoriInformasiPublik;

class MasterDataTest extends TestCase
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

    public function test_can_view_master_data_page()
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.master-data.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Data Terpadu');
    }

    public function test_can_switch_tabs_on_master_data_page()
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.master-data.index', ['tab' => 'kategori-pemohon']));
        $response->assertStatus(200);
        $response->assertSee('Kategori Pemohon');
    }

    public function test_can_create_unit_pengolah_and_redirect_to_master_data_bidang_tab()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.unit-pengolah.store'), [
            'nama_bidang' => 'Bidang Evaluasi dan Data',
            'deskripsi' => 'Mengelola evaluasi',
        ]);

        $response->assertRedirect(route('admin.master-data.index'));
        $response->assertSessionHas('tab', 'bidang');
        $this->assertDatabaseHas('unit_pengolahs', ['nama_bidang' => 'Bidang Evaluasi dan Data']);
    }

    public function test_can_create_kategori_pemohon_and_redirect_to_master_data_tab()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.kategori-pemohon.store'), [
            'nama_kategori' => 'LSM / Ormas',
        ]);

        $response->assertRedirect(route('admin.master-data.index'));
        $response->assertSessionHas('tab', 'kategori-pemohon');
        $this->assertDatabaseHas('kategori_pemohons', ['nama_kategori' => 'LSM / Ormas']);
    }

    public function test_can_create_cara_memperoleh_and_redirect_to_master_data_tab()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.cara-memperoleh-informasi.store'), [
            'nama_cara' => 'Via Email Resmi',
            'deskripsi' => 'Pengiriman file PDF melalui surel pemohon',
        ]);

        $response->assertRedirect(route('admin.master-data.index'));
        $response->assertSessionHas('tab', 'cara-memperoleh');
        $this->assertDatabaseHas('cara_memperoleh_informasis', ['nama_cara' => 'Via Email Resmi']);
    }

    public function test_can_create_kategori_informasi_and_redirect_to_master_data_tab()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.kategori-informasi-publik.store'), [
            'nama_kategori' => 'Informasi Dikecualikan',
            'deskripsi' => 'Informasi rahasia negara / pribadi',
            'icon' => 'lock',
            'bg_color' => 'bg-rose-50',
            'text_color' => 'text-rose-600',
        ]);

        $response->assertRedirect(route('admin.master-data.index'));
        $response->assertSessionHas('tab', 'kategori-informasi');
        $this->assertDatabaseHas('kategori_informasi_publiks', ['nama_kategori' => 'Informasi Dikecualikan']);
    }

    public function test_can_update_unit_pengolah()
    {
        $unit = UnitPengolah::create(['nama_bidang' => 'Bidang Awal', 'deskripsi' => 'Awal']);

        $response = $this->actingAs($this->adminUser)->put(route('admin.unit-pengolah.update', $unit->id), [
            'nama_bidang' => 'Bidang Diperbarui',
            'deskripsi' => 'Deskripsi Baru',
        ]);

        $response->assertRedirect(route('admin.master-data.index'));
        $response->assertSessionHas('tab', 'bidang');
        $this->assertDatabaseHas('unit_pengolahs', ['nama_bidang' => 'Bidang Diperbarui']);
    }

    public function test_can_delete_unit_pengolah()
    {
        $unit = UnitPengolah::create(['nama_bidang' => 'Bidang Dihapus']);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.unit-pengolah.destroy', $unit->id));

        $response->assertRedirect(route('admin.master-data.index'));
        $response->assertSessionHas('tab', 'bidang');
        $this->assertDatabaseMissing('unit_pengolahs', ['id' => $unit->id]);
    }
}
