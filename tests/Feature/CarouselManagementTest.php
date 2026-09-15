<?php

namespace Tests\Feature;

use App\Models\CarouselItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CarouselManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Super Administrator']);
        $this->adminUser = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_landing_page_renders_active_carousels_and_hides_inactive(): void
    {
        CarouselItem::create([
            'title' => 'Slide Aktif Beranda',
            'subtitle' => 'Ini adalah deskripsi slide aktif.',
            'badge_text' => 'Aktif',
            'image_path' => 'images/carousels/slide1.svg',
            'button_text' => 'Aksi Aktif',
            'button_url' => '/permohonan/baru',
            'order' => 1,
            'is_active' => true,
        ]);

        CarouselItem::create([
            'title' => 'Slide Tersembunyi Draft',
            'subtitle' => 'Ini adalah deskripsi slide tersembunyi.',
            'badge_text' => 'Draft',
            'image_path' => 'images/carousels/slide2.svg',
            'button_text' => 'Aksi Tersembunyi',
            'button_url' => '/informasi-publik',
            'order' => 2,
            'is_active' => false,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Slide Aktif Beranda');
        $response->assertSee('Ini adalah deskripsi slide aktif.');
        $response->assertSee('Aksi Aktif');
        $response->assertDontSee('Slide Tersembunyi Draft');
    }

    public function test_unauthenticated_user_cannot_access_carousel_admin(): void
    {
        $response = $this->get(route('admin.carousel.index'));
        $response->assertRedirect(route('admin.login'));

        $response = $this->get(route('admin.carousel.create'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_carousel_index_page(): void
    {
        CarouselItem::create([
            'title' => 'Slide Banner Pertama',
            'subtitle' => 'Deskripsi slide pertama.',
            'image_path' => 'images/carousels/slide1.svg',
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.carousel.index'));

        $response->assertStatus(200);
        $response->assertSee('Banner Carousel Landing Page');
        $response->assertSee('Slide Banner Pertama');
        $response->assertSee('Tambah Slide Banner');
    }

    public function test_admin_can_create_carousel_slide_with_image(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('banner_hero.jpg', 1600, 800);

        $response = $this->actingAs($this->adminUser)->post(route('admin.carousel.store'), [
            'title' => 'Transparansi Layanan Bapperida',
            'subtitle' => 'Kemudahan akses informasi publik bagi masyarakat Ciamis.',
            'badge_text' => 'Program Unggulan',
            'image' => $image,
            'button_text' => 'Lihat Dokumen',
            'button_url' => '/informasi-publik',
            'button_target' => '_self',
            'order' => 1,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.carousel.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('carousel_items', [
            'title' => 'Transparansi Layanan Bapperida',
            'badge_text' => 'Program Unggulan',
            'button_text' => 'Lihat Dokumen',
            'is_active' => true,
        ]);

        $created = CarouselItem::where('title', 'Transparansi Layanan Bapperida')->first();
        Storage::disk('public')->assertExists($created->image_path);
    }

    public function test_admin_can_update_carousel_slide(): void
    {
        Storage::fake('public');

        $initialImage = UploadedFile::fake()->image('old_banner.jpg');
        $initialPath = $initialImage->store('carousels', 'public');

        $carousel = CarouselItem::create([
            'title' => 'Judul Lama',
            'subtitle' => 'Deskripsi lama',
            'badge_text' => 'Lama',
            'image_path' => $initialPath,
            'button_text' => 'Tombol Lama',
            'button_url' => '/old-url',
            'button_target' => '_self',
            'order' => 1,
            'is_active' => true,
        ]);

        $newImage = UploadedFile::fake()->image('new_banner.png');

        $response = $this->actingAs($this->adminUser)->put(route('admin.carousel.update', $carousel->id), [
            'title' => 'Judul Diperbarui',
            'subtitle' => 'Deskripsi diperbarui',
            'badge_text' => 'Pembaruan',
            'image' => $newImage,
            'button_text' => 'Tombol Baru',
            'button_url' => '/new-url',
            'button_target' => '_blank',
            'order' => 5,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.carousel.index'));
        $response->assertSessionHas('success');

        $carousel->refresh();
        $this->assertEquals('Judul Diperbarui', $carousel->title);
        $this->assertEquals('_blank', $carousel->button_target);
        $this->assertEquals(5, $carousel->order);

        // Old file deleted, new file exists
        Storage::disk('public')->assertMissing($initialPath);
        Storage::disk('public')->assertExists($carousel->image_path);
    }

    public function test_admin_can_toggle_carousel_status(): void
    {
        $carousel = CarouselItem::create([
            'title' => 'Test Toggle Status',
            'image_path' => 'images/carousels/slide1.svg',
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.carousel.toggle-status', $carousel->id));

        $response->assertRedirect();
        $carousel->refresh();
        $this->assertFalse($carousel->is_active);

        // Toggle back
        $response = $this->actingAs($this->adminUser)->post(route('admin.carousel.toggle-status', $carousel->id));
        $carousel->refresh();
        $this->assertTrue($carousel->is_active);
    }

    public function test_admin_can_delete_carousel_slide_and_storage_image_is_removed(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('banner_to_delete.jpg');
        $storedPath = $image->store('carousels', 'public');

        $carousel = CarouselItem::create([
            'title' => 'Slide Yang Akan Dihapus',
            'image_path' => $storedPath,
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.carousel.destroy', $carousel->id));

        $response->assertRedirect(route('admin.carousel.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('carousel_items', ['id' => $carousel->id]);
        Storage::disk('public')->assertMissing($storedPath);
    }

    public function test_carousel_validation_rules_work(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.carousel.store'), [
            'title' => '',
            'order' => 'not-a-number',
            'button_target' => 'invalid_target',
        ]);

        $response->assertSessionHasErrors(['title', 'image', 'order', 'button_target']);
    }
}
