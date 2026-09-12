<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\PermohonanInformasi;
use App\Mail\PermohonanTerkirimMail;
use Illuminate\Support\Facades\Mail;

class MailDeliveryTest extends TestCase
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

    public function test_mail_test_artisan_command_sends_email_successfully()
    {
        Mail::fake();

        $this->artisan('mail:test test@example.com')
            ->expectsOutputToContain('UJI COBA PENGIRIMAN EMAIL E-PPID')
            ->expectsOutputToContain('BERHASIL! Email uji coba berhasil dikirim ke: test@example.com')
            ->assertExitCode(0);

        Mail::assertSent(PermohonanTerkirimMail::class, function ($mail) {
            return $mail->hasTo('test@example.com') &&
                   str_contains($mail->envelope()->subject, 'Bukti Pendaftaran Permohonan Informasi Publik');
        });
    }

    public function test_admin_can_send_test_email_from_settings_page()
    {
        Mail::fake();

        $response = $this->actingAs($this->adminUser)->post(route('admin.settings.test-email'), [
            'test_email' => 'admin_test@ciamiskab.go.id',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        Mail::assertSent(PermohonanTerkirimMail::class, function ($mail) {
            return $mail->hasTo('admin_test@ciamiskab.go.id');
        });
    }

    public function test_permohonan_terkirim_mail_renders_view_properly()
    {
        $permohonan = new PermohonanInformasi([
            'nomor_registrasi' => 'REG-202609-TEST999',
            'nama_pemohon' => 'Budi Sudrajat',
            'nik_atau_no_badan_hukum' => '3207011234560001',
            'no_telp' => '+6281234567890',
            'email' => 'budi@example.com',
            'alamat' => 'Desa Ciamis, Kec. Ciamis, Kab. Ciamis',
            'subjek_informasi' => 'Data Rencana Pembangunan 2026',
            'rincian_informasi' => 'Meminta dokumen detail RKPD Tahun 2026.',
            'tujuan_penggunaan' => 'Penyusunan Skripsi',
            'status' => 'diajukan',
        ]);
        $permohonan->created_at = now();

        $mailable = new PermohonanTerkirimMail($permohonan);

        $mailable->assertSeeInHtml('REG-202609-TEST999');
        $mailable->assertSeeInHtml('Budi Sudrajat');
        $mailable->assertSeeInHtml('Data Rencana Pembangunan 2026');
        $mailable->assertSeeInHtml('Meminta dokumen detail RKPD Tahun 2026.');
        $mailable->assertSeeInHtml('PPID Bappeda Kabupaten Ciamis');
    }
}
