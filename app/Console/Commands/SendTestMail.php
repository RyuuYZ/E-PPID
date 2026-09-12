<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\PermohonanInformasi;
use App\Mail\PermohonanTerkirimMail;

class SendTestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email? : Alamat email penerima uji coba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uji coba pengiriman email konfirmasi dan kode invoice E-PPID ke alamat email tertentu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if (!$email) {
            $email = $this->ask('Masukkan alamat email tujuan uji coba');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Format email tidak valid: ' . $email);
            return Command::FAILURE;
        }

        $mailer = config('mail.default');
        $host = config("mail.mailers.{$mailer}.host", '-');
        $port = config("mail.mailers.{$mailer}.port", '-');
        $username = config("mail.mailers.{$mailer}.username", '-');
        $encryption = config("mail.mailers.{$mailer}.encryption", config("mail.mailers.{$mailer}.scheme", 'auto'));
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $this->info("=================================================");
        $this->info("📧 UJI COBA PENGIRIMAN EMAIL E-PPID");
        $this->info("=================================================");
        $this->table(
            ['Parameter', 'Nilai Saat Ini'],
            [
                ['Mailer Driver', $mailer],
                ['Host', $host],
                ['Port', $port],
                ['Enkripsi/Scheme', $encryption ?? 'auto'],
                ['Username', $username ? substr($username, 0, 3) . '***' : '(Kosong/Null)'],
                ['From Address', $fromAddress],
                ['From Name', $fromName],
                ['Tujuan Email', $email],
            ]
        );

        if ($mailer === 'log') {
            $this->warn("⚠️ Catatan: Konfigurasi saat ini menggunakan MAIL_MAILER=log.");
            $this->warn("   Email tidak akan dikirimkan ke internet/inbox asli, melainkan dicatat di storage/logs/laravel.log.");
            $this->line("   Untuk mengirim email nyata, ubah MAIL_MAILER=smtp di file .env dan isi kredensial SMTP Anda.\n");
        }

        $this->line("⏳ Mengirimkan email uji coba dengan kode invoice dummy...");

        // Buat objek dummy permohonan untuk pratinjau isi email asli
        $dummy = new PermohonanInformasi([
            'nomor_registrasi' => 'REG-TEST-' . date('YmdHis') . '-' . rand(1000, 9999),
            'nama_pemohon' => 'Pengguna Uji Coba (Tester)',
            'nik_atau_no_badan_hukum' => '3207019999990001',
            'no_telp' => '+6281234567890',
            'email' => $email,
            'alamat' => 'Jl. Jenderal Sudirman No. 16, Ciamis',
            'subjek_informasi' => 'Uji Coba Pengiriman Kode Invoice E-PPID',
            'rincian_informasi' => 'Ini adalah pesan email percobaan untuk memverifikasi fungsionalitas pengiriman email notifikasi dan kode invoice sistem E-PPID Bappeda Ciamis.',
            'tujuan_penggunaan' => 'Verifikasi Sistem Pengiriman Email',
            'status' => 'diajukan',
        ]);
        $dummy->created_at = now();

        try {
            Mail::to($email)->send(new PermohonanTerkirimMail($dummy));

            $this->newLine();
            $this->info("✅ BERHASIL! Email uji coba berhasil dikirim ke: {$email}");
            $this->info("📌 Kode Invoice Dummy: {$dummy->nomor_registrasi}");
            if ($mailer === 'log') {
                $this->line("👉 Periksa log di storage/logs/laravel.log untuk melihat template email yang dicatat.");
            } else {
                $this->line("👉 Silakan periksa Kotak Masuk (Inbox) atau folder Spam pada akun email {$email}.");
            }
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error("❌ GAGAL MENGIRIM EMAIL!");
            $this->error("Pesan Kesalahan: " . $e->getMessage());

            $this->newLine();
            $this->warn("💡 PANDUAN PENYELESAIAN MASALAH (TROUBLESHOOTING):");
            if (str_contains(strtolower($e->getMessage()), '535') || str_contains(strtolower($e->getMessage()), 'auth')) {
                $this->line("1. Kredensial (Username/Password) salah atau ditolak oleh SMTP server.");
                $this->line("   - Jika menggunakan Gmail: Anda WAJIB membuat 'App Password' (16 karakter) di Google Account.");
                $this->line("     Buka https://myaccount.google.com/apppasswords lalu buat password untuk aplikasi PPID.");
            } elseif (str_contains(strtolower($e->getMessage()), 'connection') || str_contains(strtolower($e->getMessage()), 'timed out')) {
                $this->line("1. Koneksi ke SMTP Host gagal atau timeout.");
                $this->line("   - Periksa apakah host dan port sudah sesuai (Gmail: smtp.gmail.com port 587/465).");
                $this->line("   - Pastikan jaringan internet / firewall tidak memblokir port SMTP.");
            } else {
                $this->line("1. Periksa file .env dan pastikan nilai MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD sudah terisi.");
                $this->line("2. Jalankan 'php artisan config:clear' setelah mengubah file .env.");
            }

            return Command::FAILURE;
        }
    }
}
