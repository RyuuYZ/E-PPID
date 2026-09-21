@extends('layouts.public')

@section('title', 'Formulir Permohonan Informasi Publik - Bappeda Kabupaten Ciamis')

@section('head')
<!-- Cloudflare Turnstile API -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endsection

@section('styles')
<style>
    /* Custom clean styling */
    input[type=file]::file-selector-button {
        display: none;
    }
</style>
@endsection

@section('content')
<main class="flex-grow py-10 md:py-16 px-4 sm:px-6 bg-[#f8fafc]">
<div class="max-w-4xl mx-auto w-full">

    <!-- Top Navigation / Breadcrumb -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-blue-700 transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
            Formulir Permohonan Informasi Publik
        </h1>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
            Sesuai UU No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik. Silakan isi data berikut dengan benar dan lengkap.
        </p>
    </div>

    <!-- Validation Error Alert Summary -->
    @if ($errors->any())
    <div class="mb-6 p-4 sm:p-5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 animate-fade-in">
        <div class="flex items-center gap-2 mb-2 font-bold text-rose-900 text-sm">
            <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
            <span>Perhatian: Formulir belum dapat dikirim</span>
        </div>
        <ul class="list-disc list-inside space-y-1 text-xs text-rose-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form -->
    <form action="{{ route('permohonan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="permohonanForm">
        @csrf

        <!-- Section 1: Data Identitas Pemohon -->
        <section class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs">
            <div class="mb-5 pb-3 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Identitas Pemohon</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Nama Lengkap -->
                <div class="flex flex-col gap-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-700" for="namaLengkap">
                        Nama Lengkap Sesuai KTP / Identitas <span class="text-rose-500">*</span>
                    </label>
                    <input class="h-11 px-3.5 border @error('nama_pemohon') border-rose-300 bg-rose-50/20 @else border-slate-200 bg-white @enderror rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-slate-900 placeholder:text-slate-400 text-sm" 
                           id="namaLengkap" name="nama_pemohon" value="{{ old('nama_pemohon') }}" placeholder="Contoh: Ahmad Hidayat" required type="text">
                    @error('nama_pemohon')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIK -->
                <div class="flex flex-col gap-1.5">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-semibold text-slate-700" for="nik">
                            NIK / No. Identitas <span class="text-rose-500">*</span>
                        </label>
                        <span id="nik-counter" class="text-[11px] text-slate-400 font-mono">{{ strlen(old('nik_atau_no_badan_hukum', '')) }}/16 digit</span>
                    </div>
                    <input class="h-11 px-3.5 border @error('nik_atau_no_badan_hukum') border-rose-300 bg-rose-50/20 @else border-slate-200 bg-white @enderror rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-slate-900 placeholder:text-slate-400 text-sm font-mono" 
                           id="nik" 
                           name="nik_atau_no_badan_hukum" 
                           value="{{ old('nik_atau_no_badan_hukum') }}"
                           placeholder="16 digit angka NIK" 
                           required 
                           type="text" 
                           inputmode="numeric" 
                           maxlength="16" 
                           pattern="[0-9]{1,16}" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16); document.getElementById('nik-counter').innerText = this.value.length + '/16 digit';">
                    @error('nik_atau_no_badan_hukum')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori Pemohon -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700" for="kategori_pemohon">
                        Kategori Pemohon <span class="text-rose-500">*</span>
                    </label>
                    <select class="custom-select w-full text-xs font-medium" id="kategori_pemohon" name="kategori_pemohon_id" data-placeholder="-- Pilih Kategori --" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriPemohons as $kp)
                            <option value="{{ $kp->id }}" {{ old('kategori_pemohon_id') == $kp->id ? 'selected' : '' }}>{{ $kp->nama_kategori }}</option>
                        @endforeach
                    </select>
                    @error('kategori_pemohon_id')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pekerjaan -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700" for="pekerjaan">
                        Pekerjaan / Profesi
                    </label>
                    <input class="h-11 px-3.5 border border-slate-200 bg-white rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-slate-900 placeholder:text-slate-400 text-sm" 
                           id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}" placeholder="Contoh: Mahasiswa, Peneliti, Karyawan Swasta" type="text">
                </div>

                <!-- No. Telepon / WhatsApp -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700" for="telepon_display">
                        No. Telepon / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex rounded-xl border @error('no_telp') border-rose-300 bg-rose-50/20 @else border-slate-200 bg-white @enderror overflow-hidden focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-100 transition-all h-11">
                        <span class="inline-flex items-center px-3.5 bg-slate-100 border-r border-slate-200 text-slate-600 font-semibold text-xs select-none font-mono">
                            +62
                        </span>
                        <input class="h-full px-3 flex-1 bg-transparent outline-none text-slate-900 placeholder:text-slate-400 text-sm font-mono" 
                               id="telepon_display" 
                               value="{{ old('no_telp') ? preg_replace('/^\+62/', '', old('no_telp')) : '' }}"
                               placeholder="81234567890" 
                               required 
                               type="text" 
                               inputmode="numeric" 
                               maxlength="13" 
                               oninput="handlePhoneInput(this)">
                    </div>
                    <input type="hidden" name="no_telp" id="telepon" value="{{ old('no_telp') }}" required>
                    @error('no_telp')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="flex flex-col gap-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-700" for="email">
                        Alamat Email Aktif <span class="text-rose-500">*</span>
                    </label>
                    <input class="h-11 px-3.5 border @error('email') border-rose-300 bg-rose-50/20 @else border-slate-200 bg-white @enderror rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-slate-900 placeholder:text-slate-400 text-sm" 
                           id="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required type="email">
                    <p class="text-[11px] text-slate-400">Kode invoice & perkembangan permohonan akan dikirimkan ke email ini.</p>
                    @error('email')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sub-divider Wilayah -->
                <div class="sm:col-span-2 pt-3 border-t border-slate-100">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Wilayah Domisili</span>
                </div>

                <!-- Kabupaten -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700">Kabupaten / Kota</label>
                    <input type="text" value="Kabupaten Ciamis" readonly 
                           class="h-11 px-3.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-700 font-semibold cursor-not-allowed select-none outline-none text-sm">
                    <input type="hidden" name="kabupaten" value="Kabupaten Ciamis">
                </div>

                <!-- Kecamatan -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700" for="select_kecamatan">
                        Kecamatan <span class="text-rose-500">*</span>
                    </label>
                    <select id="select_kecamatan" name="kecamatan" required onchange="handleKecamatanChange()"
                            class="h-11 px-3.5 border border-slate-200 rounded-xl bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-slate-900 text-sm">
                        <option value="">-- Pilih Kecamatan --</option>
                    </select>
                </div>

                <!-- Desa / Kelurahan -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700" for="select_desa">
                        Desa / Kelurahan <span class="text-rose-500">*</span>
                    </label>
                    <select id="select_desa" name="desa" required onchange="updateFullAlamat()" disabled
                            class="h-11 px-3.5 border border-slate-200 rounded-xl bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-slate-900 text-sm disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed">
                        <option value="">-- Pilih Kecamatan Dahulu --</option>
                    </select>
                </div>

                <!-- Detail Alamat -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700" for="detail_alamat">
                        Jalan / Dusun / RT / RW
                    </label>
                    <input type="text" id="detail_alamat" name="detail_alamat" value="{{ old('detail_alamat') }}" oninput="updateFullAlamat()"
                           placeholder="Contoh: Jl. Jend. Sudirman No. 16, RT 01/RW 02" 
                           class="h-11 px-3.5 border border-slate-200 bg-white rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-slate-900 placeholder:text-slate-400 text-sm">
                </div>

                <!-- Pratinjau Alamat Lengkap -->
                <div class="flex flex-col gap-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-700" for="alamat">
                        Pratinjau Alamat Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <textarea class="p-3 border @error('alamat') border-rose-300 bg-rose-50/20 @else border-slate-200 bg-slate-50 @enderror rounded-xl outline-none transition-all text-slate-800 text-xs resize-none font-medium leading-relaxed" 
                              id="alamat" name="alamat" placeholder="Pilih Kecamatan dan Desa di atas untuk menghasilkan alamat lengkap..." required rows="2" readonly>{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <!-- Section 2: Rincian Informasi Publik -->
        <section class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs">
            <div class="mb-5 pb-3 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Informasi yang Diminta</h2>
            </div>

            <div class="flex flex-col gap-5">
                <!-- Judul Informasi -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700" for="subjek">
                        Judul / Pokok Informasi yang Diminta <span class="text-rose-500">*</span>
                    </label>
                    <input class="h-11 px-3.5 border @error('subjek_informasi') border-rose-300 bg-rose-50/20 @else border-slate-200 bg-white @enderror rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-slate-900 placeholder:text-slate-400 text-sm" 
                           id="subjek" name="subjek_informasi" value="{{ old('subjek_informasi') }}" placeholder="Contoh: Dokumen RTRW Kabupaten Ciamis Tahun 2024-2044" required type="text">
                    @error('subjek_informasi')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Isi Rincian Informasi -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700" for="deskripsi">
                        Rincian / Uraian Informasi yang Dibutuhkan <span class="text-rose-500">*</span>
                    </label>
                    <textarea class="p-3.5 border @error('rincian_informasi') border-rose-300 bg-rose-50/20 @else border-slate-200 bg-white @enderror rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-slate-900 placeholder:text-slate-400 text-sm resize-none leading-relaxed" 
                              id="deskripsi" name="rincian_informasi" placeholder="Jelaskan secara spesifik bab, tabel, data statistik, atau dokumen yang Anda perlukan..." required rows="4">{{ old('rincian_informasi') }}</textarea>
                    @error('rincian_informasi')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tujuan Penggunaan -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700" for="tujuan">
                        Tujuan Penggunaan Informasi <span class="text-rose-500">*</span>
                    </label>
                    <textarea class="p-3.5 border @error('tujuan_penggunaan') border-rose-300 bg-rose-50/20 @else border-slate-200 bg-white @enderror rounded-xl focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-slate-900 placeholder:text-slate-400 text-sm resize-none leading-relaxed" 
                              id="tujuan" name="tujuan_penggunaan" placeholder="Jelaskan untuk keperluan apa informasi ini digunakan (Contoh: Riset Akademis, Tesis Magister, Kajian Publik)" required rows="3">{{ old('tujuan_penggunaan') }}</textarea>
                    @error('tujuan_penggunaan')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <!-- Section 3: Cara Memperoleh Informasi -->
        <section class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs">
            <div class="mb-5 pb-3 border-b border-slate-100 flex items-start justify-between flex-wrap gap-2">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Cara Memperoleh Informasi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih metode bagaimana Anda ingin menerima berkas dokumen informasi publik yang diminta.</p>
                </div>
            </div>

            <div class="flex flex-col gap-3.5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    @php
                        // Sort so that the recommended method (Email) is displayed first on the left
                        $sortedCaraMemperoleh = $caraMemperoleh->sortByDesc(function($item) {
                            return str_contains(strtolower($item->nama_cara), 'email') ? 1 : 0;
                        });
                    @endphp
                    @foreach($sortedCaraMemperoleh as $cara)
                    @php
                        $isEmail = str_contains(strtolower($cara->nama_cara), 'email');
                        $isCetak = str_contains(strtolower($cara->nama_cara), 'cetak') || str_contains(strtolower($cara->nama_cara), 'hardcopy');
                        
                        // Default to Email if no old input is present, or match old input
                        if (old('cara_memperoleh_informasi_id')) {
                            $isSelected = old('cara_memperoleh_informasi_id') == $cara->id;
                        } else {
                            $isSelected = $isEmail;
                        }
                    @endphp
                    <label class="relative flex flex-col justify-between p-4 border rounded-2xl cursor-pointer transition-all {{ $isEmail ? 'border-blue-300 bg-blue-50/20 hover:bg-blue-50/50 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300' }} has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/70 has-[:checked]:ring-2 has-[:checked]:ring-blue-100 group">
                        
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-2.5">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100 text-slate-600 group-hover:bg-slate-200 transition-colors">
                                    @if($isEmail)
                                        <span class="material-symbols-outlined text-[20px]">mail</span>
                                    @elseif($isCetak)
                                        <span class="material-symbols-outlined text-[20px]">print</span>
                                    @else
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    @endif
                                </div>

                                <input class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500 mt-1 cursor-pointer" 
                                       name="cara_memperoleh_informasi_id" type="radio" value="{{ $cara->id }}" {{ $isSelected ? 'checked' : '' }} required>
                            </div>

                            <div class="flex items-center gap-1.5 flex-wrap mb-1.5">
                                <span class="text-xs font-bold text-slate-900 leading-snug">{{ $cara->nama_cara }}</span>
                                @if($isEmail)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                        <span class="material-symbols-outlined text-[12px]">recommend</span>
                                        Disarankan
                                    </span>
                                @endif
                            </div>

                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ $cara->deskripsi }}</p>
                        </div>

                        @if($isEmail)
                        <div class="mt-3 pt-2.5 border-t border-blue-200/60 flex items-center gap-1.5 text-[10.5px] text-blue-700 font-semibold">
                            <span class="material-symbols-outlined text-[14px]">bolt</span>
                            <span>Cepat & langsung terkirim ke email</span>
                        </div>
                        @endif
                    </label>
                    @endforeach
                </div>

                <!-- Banner Rekomendasi Pengiriman Email -->
                <div class="flex items-start gap-3 p-3.5 sm:p-4 rounded-xl bg-blue-50/80 border border-blue-100 text-slate-700 text-xs leading-relaxed">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 shadow-2xs">
                        <span class="material-symbols-outlined text-[18px]">forward_to_inbox</span>
                    </div>
                    <div>
                        <span class="font-bold text-blue-900 block mb-0.5">Saran Pengiriman Dokumen:</span>
                        <span>Disarankan memilih pengiriman melalui <strong>Email (Softcopy)</strong> agar dokumen informasi resmi dan surat tanggapan dari PPID Bapperida dapat diterima secara instan, aman, serta dapat langsung diunduh tanpa perlu datang fisik ke kantor.</span>
                    </div>
                </div>

                @error('cara_memperoleh_informasi_id')
                    <p class="text-[11px] text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <!-- Section 4: Unggah Dokumen Pendukung -->
        <section class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs">
            <div class="mb-5 pb-3 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Dokumen Identitas</h2>
            </div>

            <div class="flex flex-col gap-3">
                <div x-data="fileUpload()" 
                     @dragover.prevent="dragover = true" 
                     @dragleave.prevent="dragover = false" 
                     @drop.prevent="handleDrop"
                     :class="dragover ? 'border-blue-500 bg-blue-50/30' : (error ? 'border-rose-300 bg-rose-50/20' : (file ? 'border-emerald-400 bg-emerald-50/20' : 'border-slate-300 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-400'))"
                     class="border-2 border-dashed rounded-xl p-6 sm:p-8 flex flex-col items-center justify-center text-center transition-all relative">
                    
                    <span class="material-symbols-outlined text-[36px] mb-2 transition-colors" 
                          :class="error ? 'text-rose-500' : (file ? 'text-emerald-600' : 'text-slate-400')" 
                          x-text="error ? 'error' : (file ? 'check_circle' : 'cloud_upload')">cloud_upload</span>
                    
                    <input type="file" id="dokumenKtp" name="file_identitas" :required="!file" accept=".jpg,.jpeg,.png,.pdf" 
                           @change="handleFileChange" class="hidden" x-ref="fileInput">
                    
                    <!-- Default state (no file) -->
                    <div x-show="!file && !error">
                        <p class="text-xs text-slate-600 font-medium">
                            <button type="button" @click="$refs.fileInput.click()" class="text-blue-700 font-semibold hover:underline">
                                Pilih Berkas
                            </button>
                            atau tarik dan lepas ke sini
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, PDF (Maksimal 5MB)</p>
                    </div>

                    <!-- Selected file state -->
                    <div x-show="file && !error" class="flex flex-col items-center" style="display: none;">
                        <p class="text-xs font-bold text-slate-800" x-text="file?.name"></p>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="fileSize"></p>
                        <button type="button" @click="resetFile()" class="mt-2 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:underline">
                            Ganti Berkas Lain
                        </button>
                    </div>

                    <!-- Error state -->
                    <div x-show="error" class="flex flex-col items-center" style="display: none;">
                        <p class="text-xs font-semibold text-rose-600" x-text="errorMessage"></p>
                        <button type="button" @click="resetFile()" class="mt-2 text-xs font-semibold text-blue-700 hover:underline">
                            Pilih Berkas Lain
                        </button>
                    </div>
                </div>

                @error('file_identitas')
                    <p class="text-[11px] text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <!-- Section 5: Verifikasi Keamanan Anti-Spam (Cloudflare Turnstile) -->
        <section class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs">
            <div class="mb-5 pb-3 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Verifikasi Keamanan</h2>
            </div>

            <!-- Honeypot Bot Trap Field (Hidden from humans) -->
            <input type="text" name="_hp_website" value="" class="hidden" tabindex="-1" autocomplete="off" style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;">

            <div class="space-y-3">
                <!-- Turnstile Container -->
                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>

                @error('cf-turnstile-response')
                    <div class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-200 text-amber-700 text-xs rounded-xl font-medium">
                        <span class="material-symbols-outlined text-[16px] text-amber-500 shrink-0">warning</span>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
                @error('captcha')
                    <div class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-200 text-amber-700 text-xs rounded-xl font-medium">
                        <span class="material-symbols-outlined text-[16px] text-amber-500 shrink-0">warning</span>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </section>

        <!-- Bottom Action Bar & Disclaimer -->
        <div class="pt-2 space-y-4">
            <div class="flex items-start gap-2.5 p-4 bg-slate-100/80 rounded-xl border border-slate-200 text-slate-600 text-xs leading-relaxed">
                <span class="material-symbols-outlined text-slate-500 text-[18px] shrink-0 mt-0.5">lock</span>
                <p>
                    Data pribadi dan dokumen identitas Anda dilindungi kerahasiaannya sesuai regulasi yang berlaku dan semata-mata digunakan untuk kepentingan validasi permohonan informasi publik.
                </p>
            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-end items-center gap-3 pt-2">
                <a href="{{ route('home') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 border border-slate-300 transition-colors text-center">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-7 py-3 rounded-xl text-xs font-bold text-white bg-[#03224d] hover:bg-[#0B1B3D] transition-all shadow-md shadow-blue-950/10 hover:shadow-lg text-center flex items-center justify-center gap-2 active:scale-[0.99]">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    <span>Kirim Permohonan</span>
                </button>
            </div>
        </div>
    </form>
</div>
</main>

<script>
document.addEventListener('alpine:init', () => {
    // File Upload Alpine Component
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

        validateAndSetFile(selectedFile) {
            this.error = false;
            this.errorMessage = '';

            const validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            const fileName = selectedFile.name.toLowerCase();
            const fileExt = fileName.split('.').pop();

            if (!validExtensions.includes(fileExt)) {
                this.rejectFile('Format file (.' + fileExt + ') tidak diizinkan. Harap unggah berkas JPG, PNG, atau PDF.');
                return;
            }

            if (selectedFile.size > maxSize) {
                this.rejectFile('Ukuran file melebihi batas maksimal 5MB.');
                return;
            }

            if (selectedFile.size === 0) {
                this.rejectFile('Berkas kosong (0 byte).');
                return;
            }

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(selectedFile);
            this.$refs.fileInput.files = dataTransfer.files;

            this.file = selectedFile;
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
