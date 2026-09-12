@extends('admin.layouts.settings')

@section('settings_content')
<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 text-sm text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm flex items-start gap-3">
            <span class="material-symbols-outlined text-green-600 text-xl shrink-0 mt-0.5">check_circle</span>
            <div class="flex-1 font-medium leading-relaxed">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm flex items-start gap-3">
            <span class="material-symbols-outlined text-red-600 text-xl shrink-0 mt-0.5">error</span>
            <div class="flex-1 font-medium leading-relaxed">{{ session('error') }}</div>
        </div>
    @endif

    <!-- 1. Form Konfigurasi Umum -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[#03224d]">tune</span>
                <h3 class="text-sm font-bold text-gray-800">Konfigurasi Identitas Portal</h3>
            </div>
            <span class="text-xs text-gray-500 font-medium">Pengaturan Utama</span>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <!-- App Name -->
                <div class="flex flex-col gap-1.5">
                    <label for="app_name" class="text-xs font-semibold text-gray-700">Nama Aplikasi / Portal</label>
                    <input type="text" id="app_name" name="app_name" value="{{ $settings['app_name']->value ?? 'E-PPID Bappeda Ciamis' }}" class="w-full text-sm border-gray-300 rounded-lg focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 h-10 px-3 shadow-sm">
                </div>

                <!-- Contact Email -->
                <div class="flex flex-col gap-1.5">
                    <label for="contact_email" class="text-xs font-semibold text-gray-700">Email Kontak Resmi</label>
                    <input type="email" id="contact_email" name="contact_email" value="{{ $settings['contact_email']->value ?? 'bappeda@ciamiskab.go.id' }}" class="w-full text-sm border-gray-300 rounded-lg focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 h-10 px-3 shadow-sm">
                </div>

                <!-- Contact Phone -->
                <div class="flex flex-col gap-1.5">
                    <label for="contact_phone" class="text-xs font-semibold text-gray-700">Nomor Telepon Resmi</label>
                    <input type="text" id="contact_phone" name="contact_phone" value="{{ $settings['contact_phone']->value ?? '(0265) 771033' }}" class="w-full text-sm border-gray-300 rounded-lg focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 h-10 px-3 shadow-sm">
                </div>
                
                <!-- Office Address -->
                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label for="office_address" class="text-xs font-semibold text-gray-700">Alamat Kantor</label>
                    <textarea id="office_address" name="office_address" class="w-full text-sm border-gray-300 rounded-lg focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 p-3 shadow-sm" rows="2">{{ $settings['office_address']->value ?? 'Jl. Jenderal Sudirman No.16, Ciamis' }}</textarea>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5 mt-6">
                <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-4">Pengaturan Keamanan & Sistem</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Maintenance Mode -->
                    <div class="flex items-start gap-3 p-3.5 rounded-lg border border-gray-200 bg-gray-50/50 hover:bg-white transition-colors">
                        <div class="flex items-center h-5">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" class="rounded text-[#1a2b42] focus:ring-[#1a2b42] border-gray-300 h-4 w-4 mt-0.5" {{ ($settings['maintenance_mode']->value ?? '0') == '1' ? 'checked' : '' }}>
                        </div>
                        <div class="flex flex-col">
                            <label for="maintenance_mode" class="text-sm font-semibold text-gray-700 cursor-pointer">Mode Pemeliharaan (Maintenance Mode)</label>
                            <p class="text-xs text-gray-500 mt-0.5">Jika diaktifkan, situs publik akan menampilkan halaman pemeliharaan. Hanya Admin yang dapat login.</p>
                        </div>
                    </div>
                    
                    <!-- Enforce 2FA -->
                    <div class="flex items-start gap-3 p-3.5 rounded-lg border border-gray-200 bg-gray-50/50 hover:bg-white transition-colors">
                        <div class="flex items-center h-5">
                            <input type="hidden" name="enforce_2fa" value="0">
                            <input type="checkbox" id="enforce_2fa" name="enforce_2fa" value="1" class="rounded text-[#1a2b42] focus:ring-[#1a2b42] border-gray-300 h-4 w-4 mt-0.5" {{ ($settings['enforce_2fa']->value ?? '0') == '1' ? 'checked' : '' }}>
                        </div>
                        <div class="flex flex-col">
                            <label for="enforce_2fa" class="text-sm font-semibold text-gray-700 cursor-pointer">Wajibkan 2FA (Two-Factor Auth)</label>
                            <p class="text-xs text-gray-500 mt-0.5">Jika diaktifkan, seluruh pengguna diwajibkan melakukan autentikasi dua faktor untuk login.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end border-t border-gray-100">
                <button type="submit" class="inline-flex items-center gap-2 justify-center rounded-lg bg-[#03224d] px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#1f3864] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>

    <!-- 2. Status & Uji Coba Pengiriman Email / SMTP -->
    @php
        $activeMailer = config('mail.default');
        $activeHost = config("mail.mailers.{$activeMailer}.host", '-');
        $activePort = config("mail.mailers.{$activeMailer}.port", '-');
        $activeUser = config("mail.mailers.{$activeMailer}.username", '-');
        $activeFrom = config('mail.from.address');
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50/70 to-indigo-50/40 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-blue-700">mark_email_read</span>
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Status Server Email & Uji Coba Pengiriman Invoice</h3>
                    <p class="text-xs text-gray-500">Verifikasi koneksi SMTP untuk pengiriman otomatis kode invoice / registrasi ke pemohon.</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $activeMailer === 'smtp' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $activeMailer === 'smtp' ? 'bg-emerald-500' : 'bg-amber-500' }} mr-1.5"></span>
                Driver: {{ strtoupper($activeMailer) }}
            </span>
        </div>

        <div class="p-6 space-y-6">
            <!-- Grid Status Driver -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-3.5 rounded-lg border border-gray-200 bg-gray-50/80">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block mb-1">Mailer Driver</span>
                    <span class="text-sm font-bold text-gray-900 font-mono">{{ $activeMailer }}</span>
                </div>
                <div class="p-3.5 rounded-lg border border-gray-200 bg-gray-50/80">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block mb-1">SMTP Host</span>
                    <span class="text-sm font-bold text-gray-900 font-mono truncate block" title="{{ $activeHost }}">{{ $activeHost }}</span>
                </div>
                <div class="p-3.5 rounded-lg border border-gray-200 bg-gray-50/80">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block mb-1">Port</span>
                    <span class="text-sm font-bold text-gray-900 font-mono">{{ $activePort }}</span>
                </div>
                <div class="p-3.5 rounded-lg border border-gray-200 bg-gray-50/80">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block mb-1">Sender Email</span>
                    <span class="text-sm font-bold text-gray-900 font-mono truncate block" title="{{ $activeFrom }}">{{ $activeFrom }}</span>
                </div>
            </div>

            @if(empty($activeUser) && $activeMailer === 'smtp')
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-600 text-lg shrink-0 mt-0.5">info</span>
                <div class="leading-relaxed">
                    <strong>Pemberitahuan:</strong> Kredensial <code>MAIL_USERNAME</code> dan <code>MAIL_PASSWORD</code> di file <code>.env</code> masih kosong. Untuk mengirimkan email nyata ke inbox Gmail/Yahoo pengguna:
                    <ul class="list-disc ml-5 mt-1 space-y-0.5">
                        <li>Buka file <code>.env</code> di root project.</li>
                        <li>Isi <code>MAIL_USERNAME=email_anda@gmail.com</code> dan <code>MAIL_PASSWORD=xxxx xxxx xxxx xxxx</code> (Gunakan 16 digit Google App Password).</li>
                        <li>Jalankan <code>php artisan config:clear</code> atau simpan pengaturan ini.</li>
                    </ul>
                </div>
            </div>
            @endif

            <!-- Form Uji Coba Pengiriman Email -->
            <div class="border-t border-gray-100 pt-5">
                <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Kirim Email Percobaan (Test Delivery)</h4>
                <form action="{{ route('admin.settings.test-email') }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    @csrf
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-lg">mail</span>
                        <input type="email" name="test_email" value="{{ auth()->user()->email ?? '' }}" placeholder="Masukkan email penerima (contoh: pemohon@gmail.com)" required class="w-full pl-9 pr-3 text-sm border-gray-300 rounded-lg focus:border-blue-600 focus:ring focus:ring-blue-100 h-10 shadow-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm shrink-0">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span>Kirim Email Percobaan</span>
                    </button>
                </form>
                <p class="text-[11px] text-gray-500 mt-2">
                    Sistem akan mengirimkan email konfirmasi dengan template resmi dan kode invoice dummy untuk memastikan email sampai ke kotak masuk.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
