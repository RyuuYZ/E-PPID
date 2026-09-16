@extends('layouts.public')

@section('title', 'Formulir Permohonan Informasi Publik - Bappeda PPID')

@section('styles')
<!-- Cloudflare Turnstile API -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<style>
    /* Custom file input styling */
    input[type=file]::file-selector-button {
        border: none;
        background: var(--color-primary-container, #1f3864);
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 0.5rem;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 500;
        transition: background-color 0.2s;
    }
    input[type=file]::file-selector-button:hover {
        background: var(--color-primary, #03224d);
    }
    @keyframes spin-once {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .spin-anim {
        animation: spin-once 0.6s ease-in-out;
    }
</style>
@endsection

@section('content')
<main class="flex-grow py-12 md:py-20 px-6">
<div class="max-w-3xl mx-auto w-full">
<!-- Header Section -->
<div class="mb-10 text-center md:text-left">
    <h1 class="text-3xl md:text-4xl font-bold text-[#0B1B3D] mb-3">
        Formulir Permohonan Informasi Publik
    </h1>
    <p class="text-gray-500 text-lg">
        Silakan lengkapi formulir di bawah ini untuk mengajukan permohonan informasi baru.
    </p>
</div>

<!-- Validation Error Alert Summary -->
@if ($errors->any())
<div class="mb-8 p-5 bg-red-50 border-l-4 border-red-500 rounded-2xl shadow-sm text-red-800">
    <div class="flex items-center gap-2 mb-2 font-bold text-red-900">
        <span class="material-symbols-outlined text-red-600 text-[22px]">error</span>
        <span>Perhatian: Formulir belum dapat dikirimkan</span>
    </div>
    <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Form -->
<form action="{{ route('permohonan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="permohonanForm">
@csrf

<!-- Section 1: Data Diri Pemohon -->
<section class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm transition-all hover:shadow-md">
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
            <span class="material-symbols-outlined text-[20px]" data-icon="person">person</span>
        </div>
        <h2 class="text-xl font-bold text-[#0B1B3D]">Data Diri Pemohon</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Nama Lengkap -->
        <div class="flex flex-col gap-2 md:col-span-2">
            <label class="text-sm font-semibold text-gray-700" for="namaLengkap">Nama Lengkap <span class="text-red-500">*</span></label>
            <input class="h-12 px-4 border @error('nama_pemohon') border-red-400 bg-red-50/30 @else border-gray-200 bg-gray-50 @enderror rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm" 
                   id="namaLengkap" name="nama_pemohon" value="{{ old('nama_pemohon') }}" placeholder="Masukkan nama lengkap sesuai identitas" required="" type="text">
            @error('nama_pemohon')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <!-- NIK -->
        <div class="flex flex-col gap-2">
            <div class="flex justify-between items-center">
                <label class="text-sm font-semibold text-gray-700" for="nik">NIK / No. Identitas <span class="text-red-500">*</span></label>
                <span id="nik-counter" class="text-xs text-gray-400 font-mono">{{ strlen(old('nik_atau_no_badan_hukum', '')) }}/16 digit</span>
            </div>
            <input class="h-12 px-4 border @error('nik_atau_no_badan_hukum') border-red-400 bg-red-50/30 @else border-gray-200 bg-gray-50 @enderror rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm font-mono tracking-wider" 
                   id="nik" 
                   name="nik_atau_no_badan_hukum" 
                   value="{{ old('nik_atau_no_badan_hukum') }}"
                   placeholder="Masukkan 16 digit angka NIK" 
                   required="" 
                   type="text" 
                   inputmode="numeric" 
                   maxlength="16" 
                   pattern="[0-9]{1,16}" 
                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16); document.getElementById('nik-counter').innerText = this.value.length + '/16 digit';">
            <p class="text-[11px] text-gray-400">Wajib angka (0-9) dan tidak lebih dari 16 angka.</p>
            @error('nik_atau_no_badan_hukum')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <!-- Kategori Pemohon -->
        <div class="flex flex-col gap-2 md:col-span-2">
            <label class="text-sm font-semibold text-gray-700" for="kategori_pemohon">Kategori Pemohon <span class="text-red-500">*</span></label>
            <select class="custom-select w-full text-sm font-medium" id="kategori_pemohon" name="kategori_pemohon_id" data-placeholder="Pilih Kategori Pemohon..." required>
                <option value="">Pilih Kategori Pemohon...</option>
                @foreach($kategoriPemohons as $kp)
                    <option value="{{ $kp->id }}" {{ old('kategori_pemohon_id') == $kp->id ? 'selected' : '' }}>{{ $kp->nama_kategori }}</option>
                @endforeach
            </select>
            @error('kategori_pemohon_id')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <!-- No. Telepon -->
        <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-gray-700" for="telepon_display">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
            <div class="flex rounded-xl border @error('no_telp') border-red-400 bg-red-50/30 @else border-gray-200 bg-gray-50 @enderror overflow-hidden focus-within:bg-white focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all h-12">
                <span class="inline-flex items-center px-4 bg-gray-100/90 border-r border-gray-200 text-gray-700 font-semibold text-sm select-none font-mono">
                    +62
                </span>
                <input class="h-full px-4 flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 text-sm font-mono tracking-wider" 
                       id="telepon_display" 
                       value="{{ old('no_telp') ? preg_replace('/^\+62/', '', old('no_telp')) : '' }}"
                       placeholder="81234567890" 
                       required="" 
                       type="text" 
                       inputmode="numeric" 
                       maxlength="13" 
                       oninput="handlePhoneInput(this)">
            </div>
            <input type="hidden" name="no_telp" id="telepon" value="{{ old('no_telp') }}" required>
            <p class="text-[11px] text-gray-400">Wajib angka dengan awalan kode negara +62 (Cth: +62 812 3456 7890).</p>
            @error('no_telp')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <!-- Email -->
        <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-gray-700" for="email">Alamat Email <span class="text-red-500">*</span></label>
            <input class="h-12 px-4 border @error('email') border-red-400 bg-red-50/30 @else border-gray-200 bg-gray-50 @enderror rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm" 
                   id="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required="" type="email">
            @error('email')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Wilayah & Alamat Lengkap -->
        <!-- Kabupaten (Terkunci di Ciamis) -->
        <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-gray-700">Kabupaten / Kota</label>
            <div class="relative">
                <input type="text" value="Kabupaten Ciamis" readonly 
                       class="h-12 px-4 pr-10 w-full border border-gray-200 rounded-xl bg-gray-100 text-gray-700 font-semibold cursor-not-allowed select-none outline-none text-sm">
                <input type="hidden" name="kabupaten" value="Kabupaten Ciamis">
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">lock</span>
            </div>
            <span class="text-[11px] text-blue-700 font-medium flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">lock</span> Terkunci: Layanan PPID Kabupaten Ciamis
            </span>
        </div>

        <!-- Kecamatan (Select) -->
        <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-gray-700" for="select_kecamatan">Kecamatan <span class="text-red-500">*</span></label>
            <select id="select_kecamatan" name="kecamatan" required onchange="handleKecamatanChange()"
                    class="h-12 px-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 text-sm">
                <option value="">-- Pilih Kecamatan --</option>
            </select>
        </div>

        <!-- Desa / Kelurahan (Select) -->
        <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-gray-700" for="select_desa">Desa / Kelurahan <span class="text-red-500">*</span></label>
            <select id="select_desa" name="desa" required onchange="updateFullAlamat()" disabled
                    class="h-12 px-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 text-sm disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                <option value="">-- Pilih Kecamatan Dahulu --</option>
            </select>
        </div>

        <!-- Detail Alamat (Jalan / RT / RW) -->
        <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-gray-700" for="detail_alamat">Nama Jalan / Dusun / RT / RW</label>
            <input type="text" id="detail_alamat" name="detail_alamat" value="{{ old('detail_alamat') }}" oninput="updateFullAlamat()"
                   placeholder="Cth: Jl. Jend. Sudirman No. 16, RT 01 / RW 02" 
                   class="h-12 px-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm">
        </div>

        <!-- Alamat Lengkap Hasil Gabungan -->
        <div class="flex flex-col gap-2 md:col-span-2">
            <label class="text-sm font-semibold text-gray-700" for="alamat">Pratinjau Alamat Lengkap <span class="text-red-500">*</span></label>
            <textarea class="p-3 border @error('alamat') border-red-400 bg-red-50/30 @else border-gray-200 bg-blue-50/40 @enderror rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm resize-none font-medium" 
                      id="alamat" name="alamat" placeholder="Pilih Kecamatan dan Desa di atas untuk menghasilkan alamat lengkap..." required="" rows="2" readonly>{{ old('alamat') }}</textarea>
            <p class="text-[11px] text-gray-400">Alamat lengkap otomatis terkomposisi dari pilihan wilayah Kabupaten Ciamis dan rincian jalan di atas.</p>
            @error('alamat')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
</section>

<!-- Section 2: Rincian Informasi yang Dibutuhkan -->
<section class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm transition-all hover:shadow-md">
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
            <span class="material-symbols-outlined text-[20px]" data-icon="description">description</span>
        </div>
        <h2 class="text-xl font-bold text-[#0B1B3D]">Rincian Informasi yang Dibutuhkan</h2>
    </div>
    <div class="flex flex-col gap-6">
        <!-- Judul Informasi -->
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <label class="text-sm font-semibold text-gray-700" for="subjek">Judul / Pokok Informasi <span class="text-red-500">*</span></label>
                <span class="text-[11px] text-gray-400">Judul Dokumen / Subjek</span>
            </div>
            <input class="h-12 px-4 border @error('subjek_informasi') border-red-400 bg-red-50/30 @else border-gray-200 bg-gray-50 @enderror rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm" 
                   id="subjek" name="subjek_informasi" value="{{ old('subjek_informasi') }}" placeholder="Masukkan judul informasi (Cth: Dokumen RPJMD Kabupaten Ciamis 2021-2026)" required="" type="text">
            @error('subjek_informasi')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <!-- Isi Rincian Informasi -->
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <label class="text-sm font-semibold text-gray-700" for="deskripsi">Isi / Uraian Rincian Informasi <span class="text-red-500">*</span></label>
                <span class="text-[11px] text-gray-400">Deskripsi Lengkap</span>
            </div>
            <textarea class="p-4 border @error('rincian_informasi') border-red-400 bg-red-50/30 @else border-gray-200 bg-gray-50 @enderror rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm resize-none" 
                      id="deskripsi" name="rincian_informasi" placeholder="Jelaskan secara rinci bab, tabel, data statistik, atau informasi spesifik yang Anda butuhkan..." required="" rows="4">{{ old('rincian_informasi') }}</textarea>
            @error('rincian_informasi')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <!-- Tujuan Penggunaan -->
        <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-gray-700" for="tujuan">Tujuan Penggunaan Informasi <span class="text-red-500">*</span></label>
            <textarea class="p-4 border @error('tujuan_penggunaan') border-red-400 bg-red-50/30 @else border-gray-200 bg-gray-50 @enderror rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm resize-none" 
                      id="tujuan" name="tujuan_penggunaan" placeholder="Jelaskan untuk apa informasi ini akan digunakan (Cth: Penelitian Akademis / Penyusunan Skripsi)" required="" rows="3">{{ old('tujuan_penggunaan') }}</textarea>
            @error('tujuan_penggunaan')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
</section>

<!-- Section 3: Cara Memperoleh Informasi -->
<section class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm transition-all hover:shadow-md">
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
            <span class="material-symbols-outlined text-[20px]" data-icon="inventory_2">inventory_2</span>
        </div>
        <h2 class="text-xl font-bold text-[#0B1B3D]">Cara Memperoleh Informasi</h2>
    </div>
    <p class="text-sm text-gray-500 mb-4">Pilih bagaimana Anda ingin menerima informasi tersebut:</p>
    <div class="flex flex-col gap-3">
        @foreach($caraMemperoleh as $cara)
        <label class="flex items-center gap-4 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 hover:border-blue-200 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50">
            <input class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500" name="cara_memperoleh_informasi_id" type="radio" value="{{ $cara->id }}" {{ old('cara_memperoleh_informasi_id') == $cara->id ? 'checked' : '' }} required>
            <div class="flex flex-col">
                <span class="text-sm font-semibold text-gray-800">{{ $cara->nama_cara }}</span>
                <span class="text-xs text-gray-500 mt-0.5">{{ $cara->deskripsi }}</span>
            </div>
        </label>
        @endforeach
    </div>
    @error('cara_memperoleh_informasi_id')
        <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
    @enderror
</section>

<!-- Section 4: Unggah Dokumen Pendukung -->
<section class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm transition-all hover:shadow-md">
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
            <span class="material-symbols-outlined text-[20px]" data-icon="upload_file">upload_file</span>
        </div>
        <h2 class="text-xl font-bold text-[#0B1B3D]">Unggah Dokumen Pendukung</h2>
    </div>
    <div class="flex flex-col gap-3">
        <div>
            <label class="text-sm font-semibold text-gray-700">KTP / Identitas Diri Resmi <span class="text-red-500">*</span></label>
            <p class="text-xs text-gray-500 mt-1">Unggah foto/scan KTP yang jelas. Format didukung: JPG, PNG, PDF (Maks. 5MB). Isi berkas akan divalidasi keasliannya.</p>
        </div>
        <div x-data="fileUpload()" 
             @dragover.prevent="dragover = true" 
             @dragleave.prevent="dragover = false" 
             @drop.prevent="handleDrop"
             :class="dragover ? 'border-blue-500 bg-blue-50' : (error ? 'border-red-400 bg-red-50/50' : (file ? 'border-green-400 bg-green-50/30' : 'border-gray-200 bg-gray-50 hover:bg-blue-50/50 hover:border-blue-300'))"
             class="border-2 border-dashed rounded-xl p-8 flex flex-col items-center justify-center text-center transition-all relative group">
            
            <span class="material-symbols-outlined text-[42px] mb-3 transition-colors" 
                  :class="error ? 'text-red-500' : (file ? 'text-green-600' : (dragover ? 'text-blue-500' : 'text-gray-400 group-hover:text-blue-500'))" 
                  x-text="error ? 'gpp_bad' : (file ? 'verified_user' : 'cloud_upload')">cloud_upload</span>
            
            <input type="file" id="dokumenKtp" name="file_identitas" :required="!file" accept=".jpg,.jpeg,.png,.pdf" 
                   @change="handleFileChange" class="hidden" x-ref="fileInput">
            
            <div x-show="!file && !error">
                <button type="button" @click="$refs.fileInput.click()" class="text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                    Klik untuk unggah
                </button>
                <span class="text-sm text-gray-500"> atau seret dan lepas file di sini</span>
                <p class="text-xs text-gray-400 mt-2">Hanya menerima gambar asli (JPG, PNG) atau dokumen resmi PDF</p>
            </div>

            <div x-show="file && !error" class="flex flex-col items-center" style="display: none;">
                <div class="flex items-center gap-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold mb-2">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    <span>Berkas Tervalidasi</span>
                </div>
                <p class="text-sm font-semibold text-gray-800" x-text="file?.name"></p>
                <p class="text-xs text-gray-500 mt-1" x-text="fileSize"></p>
                <button type="button" @click="resetFile()" class="mt-3 text-xs font-semibold text-red-500 hover:text-red-600 underline">
                    Ganti File
                </button>
            </div>

            <div x-show="error" class="flex flex-col items-center" style="display: none;">
                <div class="flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold mb-2">
                    <span class="material-symbols-outlined text-[16px]">warning</span>
                    <span>Pemeriksaan Keamanan Gagal</span>
                </div>
                <p class="text-sm font-medium text-red-600 max-w-md" x-text="errorMessage"></p>
                <button type="button" @click="resetFile()" class="mt-3 text-xs font-semibold text-blue-600 hover:text-blue-700 underline">
                    Pilih Berkas Lain
                </button>
            </div>
        </div>
        @error('file_identitas')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>
</section>

<!-- Section 5: Verifikasi Keamanan Anti-Spam (Cloudflare Turnstile) -->
<section class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm transition-all hover:shadow-md">
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
            <span class="material-symbols-outlined text-[20px]" data-icon="verified_user">verified_user</span>
        </div>
        <div>
            <h2 class="text-xl font-bold text-[#0B1B3D]">Verifikasi Keamanan</h2>
            <p class="text-xs text-gray-500 mt-0.5">Verifikasi keamanan otomatis oleh Cloudflare Turnstile.</p>
        </div>
    </div>

    <!-- Honeypot Bot Trap Field (Hidden from humans) -->
    <input type="text" name="_hp_website" value="" class="hidden" tabindex="-1" autocomplete="off" style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;">

    <div class="space-y-3">
        <!-- Turnstile -->
        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>

        @error('cf-turnstile-response')
            <div class="flex items-center gap-2.5 p-3 bg-amber-50 border border-amber-200 text-amber-700 text-xs rounded-xl font-medium animate-fade-in">
                <span class="material-symbols-outlined text-[18px] text-amber-500 shrink-0">warning</span>
                <span>{{ $message }}</span>
            </div>
        @enderror
        @error('captcha')
            <div class="flex items-center gap-2.5 p-3 bg-amber-50 border border-amber-200 text-amber-700 text-xs rounded-xl font-medium animate-fade-in">
                <span class="material-symbols-outlined text-[18px] text-amber-500 shrink-0">warning</span>
                <span>{{ $message }}</span>
            </div>
        @enderror
    </div>
</section>

<!-- Disclaimer & Actions -->
<div class="pt-6">
    <div class="flex items-start gap-3 mb-8 bg-blue-50/80 p-5 rounded-xl border border-blue-100">
        <span class="material-symbols-outlined text-blue-600 text-[20px] mt-0.5" data-icon="info">info</span>
        <p class="text-sm text-blue-900 leading-relaxed">
            <strong>Pemberitahuan Privasi:</strong> Informasi dan dokumen yang Anda berikan akan dilindungi kerahasiaannya sesuai peraturan yang berlaku dan hanya digunakan untuk keperluan pemrosesan permohonan informasi publik.
        </p>
    </div>
    <div class="flex flex-col-reverse md:flex-row justify-end items-center gap-4">
        <a href="{{ route('home') }}" class="w-full md:w-auto px-6 py-3 rounded-xl text-sm font-semibold text-gray-600 bg-white hover:bg-gray-50 transition-colors border border-gray-200 text-center" type="button">
            Batal
        </a>
        <button class="w-full md:w-auto px-8 py-3.5 rounded-xl text-sm font-semibold text-white bg-[#03224d] hover:bg-[#0B1B3D] transition-colors shadow-[0_8px_16px_-4px_rgba(3,34,77,0.3)] hover:shadow-lg hover:-translate-y-0.5 text-center flex items-center justify-center gap-2" type="submit">
            <span class="material-symbols-outlined text-[18px]">send</span>
            <span>Kirim Permohonan</span>
        </button>
    </div>
</div>
</form>
</div>
</main>

<script>
document.addEventListener('alpine:init', () => {
    // File Upload Alpine Component with Deep Magic-Bytes & Script Scanning
    Alpine.data('fileUpload', () => ({
        dragover: false,
        file: null,
        error: false,
        errorMessage: '',
        
        get fileSize() {
            if (!this.file) return '';
            const size = (this.file.size / 1024 / 1024).toFixed(2);
            return size + ' MB';
        },

        handleDrop(e) {
            this.dragover = false;
            if (e.dataTransfer.files.length > 0) {
                this.validateAndSetFile(e.dataTransfer.files[0]);
            }
        },

        handleFileChange(e) {
            if (e.target.files.length > 0) {
                this.validateAndSetFile(e.target.files[0]);
            }
        },

        async validateAndSetFile(selectedFile) {
            this.error = false;
            this.errorMessage = '';

            const validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            const fileName = selectedFile.name.toLowerCase();
            const fileExt = fileName.split('.').pop();

            // 1. Extension check
            if (!validExtensions.includes(fileExt)) {
                this.rejectFile('Format ekstensi berkas (. ' + fileExt + ') tidak diizinkan. Harap unggah berkas JPG, PNG, atau PDF.');
                return;
            }

            // 2. Double extension prevention (e.g. script.php.jpg)
            const parts = fileName.split('.');
            if (parts.length > 2) {
                const dangerousSubExts = ['php', 'sh', 'py', 'js', 'exe', 'bat', 'cmd', 'vbs', 'phtml', 'html', 'htm', 'cgi', 'pl'];
                for (let i = 1; i < parts.length - 1; i++) {
                    if (dangerousSubExts.includes(parts[i])) {
                        this.rejectFile('Nama berkas terindikasi memiliki ekstensi ganda yang mencurigakan.');
                        return;
                    }
                }
            }

            // 3. Size check
            if (selectedFile.size > maxSize) {
                this.rejectFile('Ukuran berkas melebihi batas maksimal yang diizinkan (5MB).');
                return;
            }

            if (selectedFile.size === 0) {
                this.rejectFile('Berkas kosong (0 byte).');
                return;
            }

            // 4. Binary Magic Bytes & Content Inspection (Client-side FileReader)
            try {
                const headerBuffer = await this.readFileSlice(selectedFile, 0, 16);
                const headerBytes = new Uint8Array(headerBuffer);

                // Verify magic numbers
                let isMagicValid = false;

                if (fileExt === 'jpg' || fileExt === 'jpeg') {
                    // JPEG: FF D8 FF
                    isMagicValid = (headerBytes[0] === 0xFF && headerBytes[1] === 0xD8 && headerBytes[2] === 0xFF);
                } else if (fileExt === 'png') {
                    // PNG: 89 50 4E 47 0D 0A 1A 0A
                    isMagicValid = (headerBytes[0] === 0x89 && headerBytes[1] === 0x50 && headerBytes[2] === 0x4E && headerBytes[3] === 0x47);
                } else if (fileExt === 'pdf') {
                    // PDF: %PDF- (25 50 44 46)
                    isMagicValid = (headerBytes[0] === 0x25 && headerBytes[1] === 0x50 && headerBytes[2] === 0x44 && headerBytes[3] === 0x46);
                }

                if (!isMagicValid) {
                    this.rejectFile('Header biner berkas tidak valid. Berkas bukan dokumen gambar/PDF asli yang sah.');
                    return;
                }

                // 5. Deep Scan first 64KB for script tags / payloads
                const textChunk = await this.readFileAsText(selectedFile, 0, 65536);
                const dangerousPatterns = [
                    /<\?php/i,
                    /<\?=/i,
                    /<script[\s\S]*?>/i,
                    /<\/script>/i,
                    /^#!\s*\/(usr\/)?bin\//m,
                    /\bimport\s+(os|sys|subprocess|shutil)\b/i,
                    /\bpowershell(\.exe)?\b/i,
                    /\beval\s*\(/i,
                    /\b(system|shell_exec|passthru|exec)\s*\(/i,
                    /\/JavaScript\b/i,
                    /\/Launch\b/i
                ];

                for (const pattern of dangerousPatterns) {
                    if (pattern.test(textChunk)) {
                        this.rejectFile('Berkas ditolak: isi berkas terdeteksi mengandung kode skrip program yang berbahaya.');
                        return;
                    }
                }

            } catch (err) {
                console.error('Error verifying file content', err);
            }

            // Sync with input file so form submission works properly for dragged files
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(selectedFile);
            this.$refs.fileInput.files = dataTransfer.files;

            this.file = selectedFile;
        },

        readFileSlice(file, start, end) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = reject;
                const blob = file.slice(start, end);
                reader.readAsArrayBuffer(blob);
            });
        },

        readFileAsText(file, start, end) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = reject;
                const blob = file.slice(start, end);
                reader.readAsText(blob);
            });
        },

        rejectFile(msg) {
            this.error = true;
            this.errorMessage = msg;
            this.$refs.fileInput.value = '';
            this.file = null;
        },

        resetFile() {
            this.file = null;
            this.error = false;
            this.errorMessage = '';
            this.$refs.fileInput.value = '';
        }
    }));
});

// Data Wilayah Kabupaten Ciamis (27 Kecamatan & Kelurahan/Desa)
const ciamisWilayah = {
    "Banjarsari": ["Banjarsari", "Cibadak", "Cicapar", "Ciherang", "Ciulu", "Kawasen", "Ratawangi", "Sindangsari"],
    "Banjaranyar": ["Banjaranyar", "Cigayam", "Cikaso", "Cikupa", "Kalijaya", "Karyamukti", "Langkapsari", "Pasirlawang"],
    "Baregbeg": ["Baregbeg", "Petirhilir", "Pusakanagara", "Saguling", "Sukamaju", "Sukamulya", "Jelat", "Karang Ampel", "Mekarjaya"],
    "Ciamis": ["Ciamis", "Cigembor", "Kertasari", "Benteng", "Maleber", "Sindangrasa", "Linggasari", "Imbanagara", "Imbanagara Raya", "Pawindan", "Panyingkiran"],
    "Cidolog": ["Cidolog", "Ciparay", "Hegarmanah", "Janggala", "Jelegong"],
    "Cihaurbeuti": ["Cihaurbeuti", "Cijulang", "Cikolak", "Padamulya", "Pamokolan", "Pasirtamiang", "Sukahaji", "Sukahurip", "Sukamaju", "Sumberjaya", "Tanjungsari"],
    "Cijeungjing": ["Bojongmengger", "Cijeungjing", "Ciharalang", "Handapherang", "Karanganyar", "Karangkamulyan", "Kertabumi", "Kertaharja", "Pamalayan", "Utama"],
    "Cikoneng": ["Cikoneng", "Cimari", "Gegempalan", "Kujangsari", "Nasol", "Panaragan", "Sindangsari", "Sukasenang"],
    "Cimaragas": ["Beber", "Bojongmalang", "Cimaragas", "Jayaraksa", "Raksabaya"],
    "Cipaku": ["Bangbayang", "Buniseuri", "Cieurih", "Cipaku", "Gereba", "Jalatrang", "Mekarsari", "Muktisari", "Pusakasari", "Selacau", "Selamanik", "Sukawening", "Tanjungmulya"],
    "Cisaga": ["Bangunharja", "Cisaga", "Danasari", "Girimukti", "Kepel", "Mekarmukti", "Sidamulya", "Sukahurip", "Tanjungjaya", "Wangunjaya"],
    "Jatinagara": ["Bayasari", "Cintanagara", "Dayeuhluhur", "Jatinagara", "Mulyasari", "Sukanagara"],
    "Kawali": ["Citegem", "Karangpawitan", "Kawali", "Kawalimukti", "Linggawangi", "Margamulya", "Purwasari", "Selasari", "Sindangsari", "Talagasari", "Winduraja"],
    "Lakbok": ["Baregbeg", "Cintajaya", "Cintaratu", "Kertajaya", "Kalapasawit", "Karyamulya", "Puloerang", "Rawaapu", "Sidaharja", "Sindangangin", "Sukanagara", "Tambakreja"],
    "Lumbung": ["Awiluar", "Cikupa", "Darmaraja", "Lumbung", "Lumbungsari", "Rawa", "Sadewata", "Sukahsari"],
    "Pamarican": ["Bangunsari", "Bantarsari", "Kertahayu", "Margajaya", "Medangkang", "Neglasari", "Pamarican", "Pasirnagara", "Sidaharja", "Sidamulih", "Sukahurip", "Sukajadi", "Sukajaya", "Sukaparana"],
    "Panawangan": ["Bangunjaya", "Cinyasag", "Gardujaya", "Giriluyu", "Indragiri", "Jagabaya", "Kertajaya", "Kertayasa", "Nagarajati", "Nagarajaya", "Nagarapageuh", "Panawangan", "Sadapaingan", "Sagalaherang"],
    "Panjalu": ["Bahara", "Ciomas", "Hujungtiwu", "Kertamandala", "Mandalare", "Maparah", "Panjalu", "Sandingtaman"],
    "Panumbangan": ["Banjarangsana", "Buanamekar", "Golat", "Jayagiri", "Kertarahayu", "Medanglayang", "Panumbangan", "Payungagung", "Payungsari", "Sindangbarang", "Sindangherang", "Sindangmukti", "Sukakerta", "Tanjungmulya"],
    "Purwadadi": ["Bantardawa", "Karangpaningal", "Padaringan", "Pasirlawang", "Purwadadi", "Purwajaya", "Sidarahayu", "Sukamulya", "Kutawaringin"],
    "Rajadesa": ["Andapraja", "Purwaraja", "Rajadesa", "Sirnabaya", "Sirnajaya", "Sukaharja", "Sukajaya", "Tanjungsari", "Tanjungjaya", "Tanjungukur", "Tigaherang"],
    "Rancah": ["Bojonggedang", "Cileungsir", "Cisontrol", "Dadiharja", "Jangalaharja", "Kawunglarang", "Kiarapayung", "Patakaharja", "Rancah", "Situmukti", "Wangunsari", "Karangpari", "Giriharja"],
    "Sadananya": ["Bendasari", "Gunungsari", "Mambang", "Sadananya", "Mekarjadi", "Sukajadi", "Tanjungsari", "Werasari"],
    "Sindangkasih": ["Budiasih", "Budiharja", "Gunungcupu", "Sindangkasih", "Sukamanah", "Sukaraja", "Sukaresik", "Wanasigra"],
    "Sukadana": ["Bunter", "Ciparigi", "Margaharja", "Salakaria", "Sukadana"],
    "Sukamantri": ["Cibeureum", "Sindanglaya", "Sukamantri", "Tenggerraharja"],
    "Tambaksari": ["Kadupandak", "Karangpaningal", "Karyamekar", "Mekarsari", "Sukasari", "Tambaksari"]
};

document.addEventListener('DOMContentLoaded', function() {
    const kecSelect = document.getElementById('select_kecamatan');
    if (kecSelect) {
        Object.keys(ciamisWilayah).sort().forEach(function(kec) {
            const opt = document.createElement('option');
            opt.value = kec;
            opt.textContent = 'Kecamatan ' + kec;
            kecSelect.appendChild(opt);
        });
    }
});

function handleKecamatanChange() {
    const kecSelect = document.getElementById('select_kecamatan');
    const desaSelect = document.getElementById('select_desa');
    const selectedKec = kecSelect.value;

    desaSelect.innerHTML = '<option value="">-- Pilih Desa / Kelurahan --</option>';

    if (selectedKec && ciamisWilayah[selectedKec]) {
        desaSelect.disabled = false;
        ciamisWilayah[selectedKec].sort().forEach(function(desa) {
            const opt = document.createElement('option');
            opt.value = desa;
            opt.textContent = desa;
            desaSelect.appendChild(opt);
        });
    } else {
        desaSelect.disabled = true;
        desaSelect.innerHTML = '<option value="">-- Pilih Kecamatan Dahulu --</option>';
    }

    updateFullAlamat();
}

function updateFullAlamat() {
    const kec = document.getElementById('select_kecamatan').value;
    const desa = document.getElementById('select_desa').value;
    const detail = document.getElementById('detail_alamat').value.trim();
    const alamatTextarea = document.getElementById('alamat');

    if (!kec || !desa) {
        alamatTextarea.value = '';
        alamatTextarea.placeholder = 'Pilih Kecamatan dan Desa di atas untuk menghasilkan alamat lengkap...';
        return;
    }

    let result = '';
    if (detail) {
        result += detail + ', ';
    }
    result += 'Desa/Kel. ' + desa + ', Kec. ' + kec + ', Kab. Ciamis, Jawa Barat';
    alamatTextarea.value = result;
}

function handlePhoneInput(input) {
    let val = input.value.replace(/[^0-9]/g, '');
    if (val.startsWith('62')) {
        val = val.substring(2);
    }
    if (val.startsWith('0')) {
        val = val.replace(/^0+/, '');
    }
    input.value = val.slice(0, 13);
    const hidden = document.getElementById('telepon');
    if (hidden) {
        hidden.value = val ? ('+62' + val) : '';
    }
}
</script>

@endsection
