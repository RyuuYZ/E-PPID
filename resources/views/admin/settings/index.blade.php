@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800 m-0">Konfigurasi Sistem</h2>
        <p class="text-xs text-gray-500 mt-0.5">Atur preferensi global aplikasi seperti nama, kontak, dan integrasi pihak ketiga.</p>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded shadow-sm p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <!-- App Name -->
                <div class="flex flex-col gap-1.5">
                    <label for="app_name" class="text-xs font-semibold text-gray-700">Nama Aplikasi</label>
                    <input type="text" id="app_name" name="app_name" value="{{ $settings['app_name']->value ?? 'E-PPID Bappeda Ciamis' }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 h-10 px-3">
                </div>

                <!-- Contact Email -->
                <div class="flex flex-col gap-1.5">
                    <label for="contact_email" class="text-xs font-semibold text-gray-700">Email Kontak Resmi</label>
                    <input type="email" id="contact_email" name="contact_email" value="{{ $settings['contact_email']->value ?? 'bappeda@ciamiskab.go.id' }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 h-10 px-3">
                </div>

                <!-- Contact Phone -->
                <div class="flex flex-col gap-1.5">
                    <label for="contact_phone" class="text-xs font-semibold text-gray-700">Nomor Telepon Resmi</label>
                    <input type="text" id="contact_phone" name="contact_phone" value="{{ $settings['contact_phone']->value ?? '(0265) 771033' }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 h-10 px-3">
                </div>
                
                <!-- Office Address -->
                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label for="office_address" class="text-xs font-semibold text-gray-700">Alamat Kantor</label>
                    <textarea id="office_address" name="office_address" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 p-3" rows="2">{{ $settings['office_address']->value ?? 'Jl. Jenderal Sudirman No.16, Ciamis' }}</textarea>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5 mt-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Pengaturan Keamanan & Sistem</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Maintenance Mode -->
                    <div class="flex items-start gap-3">
                        <div class="flex items-center h-5">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" class="rounded text-[#1a2b42] focus:ring-[#1a2b42] border-gray-300 h-4 w-4 mt-0.5" {{ ($settings['maintenance_mode']->value ?? '0') == '1' ? 'checked' : '' }}>
                        </div>
                        <div class="flex flex-col">
                            <label for="maintenance_mode" class="text-sm font-semibold text-gray-700 cursor-pointer">Mode Pemeliharaan (Maintenance Mode)</label>
                            <p class="text-xs text-gray-500">Jika diaktifkan, situs publik akan menampilkan halaman perbaikan. Hanya Admin yang dapat login.</p>
                        </div>
                    </div>
                    
                    <!-- Enforce 2FA -->
                    <div class="flex items-start gap-3">
                        <div class="flex items-center h-5">
                            <input type="hidden" name="enforce_2fa" value="0">
                            <input type="checkbox" id="enforce_2fa" name="enforce_2fa" value="1" class="rounded text-[#1a2b42] focus:ring-[#1a2b42] border-gray-300 h-4 w-4 mt-0.5" {{ ($settings['enforce_2fa']->value ?? '0') == '1' ? 'checked' : '' }}>
                        </div>
                        <div class="flex flex-col">
                            <label for="enforce_2fa" class="text-sm font-semibold text-gray-700 cursor-pointer">Wajibkan 2FA (Two-Factor Auth)</label>
                            <p class="text-xs text-gray-500">Jika diaktifkan, seluruh pengguna diwajibkan melakukan pengaturan Autentikasi Dua Faktor saat login.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end border-t border-gray-100">
                <button type="submit" class="inline-flex justify-center rounded bg-[#1a2b42] px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#121c2e] focus:outline-none focus:ring-2 focus:ring-[#1a2b42] focus:ring-offset-2 transition-colors">
                    Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
