<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token']);
        
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Update Settings',
            'description' => "Pengaturan sistem diperbarui",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email|max:255',
        ], [
            'test_email.required' => 'Alamat email tujuan uji coba wajib diisi.',
            'test_email.email' => 'Format alamat email tidak valid.',
        ]);

        $email = $request->input('test_email');
        $dummy = new \App\Models\PermohonanInformasi([
            'nomor_registrasi' => 'REG-TEST-' . date('YmdHis') . '-' . rand(1000, 9999),
            'nama_pemohon' => 'Administrator PPID (Uji Coba)',
            'nik_atau_no_badan_hukum' => '3207019999990001',
            'no_telp' => '+6281234567890',
            'email' => $email,
            'alamat' => 'Jl. Jenderal Sudirman No. 16, Ciamis',
            'subjek_informasi' => 'Uji Coba Pengiriman Notifikasi & Kode Invoice',
            'rincian_informasi' => 'Email ini dikirimkan melalui panel pengaturan Super Admin untuk memverifikasi fungsionalitas email delivery.',
            'tujuan_penggunaan' => 'Verifikasi Server SMTP',
            'status' => \App\Enums\PermohonanStatus::Diajukan->value,
        ]);
        $dummy->created_at = now();

        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\PermohonanTerkirimMail($dummy));

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Test Email Delivery',
                'description' => "Mengirim email uji coba kode invoice ke {$email}",
                'ip_address' => request()->ip()
            ]);

            return redirect()->route('admin.settings.index')->with('success', "✅ Email uji coba dengan kode invoice ({$dummy->nomor_registrasi}) berhasil dikirim ke {$email}.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim email test dari admin: ' . $e->getMessage());
            return redirect()->route('admin.settings.index')->with('error', "❌ Gagal mengirim email: " . $e->getMessage() . ". Pastikan konfigurasi SMTP di file .env sudah terisi dengan benar.");
        }
    }
}
