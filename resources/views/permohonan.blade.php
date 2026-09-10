@extends('layouts.public')

@section('title', 'Formulir Permohonan Informasi Publik - Bappeda PPID')

@section('styles')
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
</style>
@endsection

@section('content')
<main class="flex-grow py-12 md:py-20 px-6">
<div class="max-w-3xl mx-auto w-full">
<!-- Header Section -->
<div class="mb-12 text-center md:text-left">
    <h1 class="text-3xl md:text-4xl font-bold text-[#0B1B3D] mb-3">
        Formulir Permohonan Informasi Publik
    </h1>
    <p class="text-gray-500 text-lg">
        Silakan lengkapi formulir di bawah ini untuk mengajukan permohonan informasi baru.
    </p>
</div>
<!-- Form -->
<form action="{{ route('permohonan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
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
            <input class="h-12 px-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm" id="namaLengkap" name="nama_pemohon" placeholder="Masukkan nama lengkap sesuai identitas" required="" type="text">
        </div>
        <!-- NIK -->
        <div class="flex flex-col gap-2">
            <div class="flex justify-between items-center">
                <label class="text-sm font-semibold text-gray-700" for="nik">NIK / No. Identitas <span class="text-red-500">*</span></label>
                <span id="nik-counter" class="text-xs text-gray-400 font-mono">0/16 digit</span>
            </div>
            <input class="h-12 px-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm font-mono tracking-wider" 
                   id="nik" 
                   name="nik_atau_no_badan_hukum" 
                   placeholder="Masukkan 16 digit angka NIK" 
                   required="" 
                   type="text" 
                   inputmode="numeric" 
                   maxlength="16" 
                   pattern="[0-9]{1,16}" 
                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16); document.getElementById('nik-counter').innerText = this.value.length + '/16 digit';">
            <p class="text-[11px] text-gray-400">Wajib angka (0-9) dan tidak lebih dari 16 angka.</p>
        </div>
        <!-- Kategori Pemohon -->
        <div class="flex flex-col gap-2 md:col-span-2">
            <label class="text-sm font-semibold text-gray-700" for="kategori_pemohon">Kategori Pemohon <span class="text-red-500">*</span></label>
            <select class="custom-select w-full text-sm font-medium" id="kategori_pemohon" name="kategori_pemohon_id" data-placeholder="Pilih Kategori Pemohon..." required>
                <option value="">Pilih Kategori Pemohon...</option>
                @foreach($kategoriPemohons as $kp)
                    <option value="{{ $kp->id }}">{{ $kp->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <!-- No. Telepon -->
        <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-gray-700" for="telepon_display">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
            <div class="flex rounded-xl border border-gray-200 overflow-hidden bg-gray-50 focus-within:bg-white focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all h-12">
                <span class="inline-flex items-center px-4 bg-gray-100/90 border-r border-gray-200 text-gray-700 font-semibold text-sm select-none font-mono">
                    +62
                </span>
                <input class="h-full px-4 flex-1 bg-transparent outline-none text-gray-800 placeholder:text-gray-400 text-sm font-mono tracking-wider" 
                       id="telepon_display" 
                       placeholder="81234567890" 
                       required="" 
                       type="text" 
                       inputmode="numeric" 
                       maxlength="13" 
                       oninput="handlePhoneInput(this)">
            </div>
            <input type="hidden" name="no_telp" id="telepon" required>
            <p class="text-[11px] text-gray-400">Wajib angka dengan awalan kode negara +62 (Cth: +62 812 3456 7890).</p>
        </div>
        <!-- Email -->
        <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-gray-700" for="email">Alamat Email <span class="text-red-500">*</span></label>
            <input class="h-12 px-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm" id="email" name="email" placeholder="email@contoh.com" required="" type="email">
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
            <input type="text" id="detail_alamat" name="detail_alamat" oninput="updateFullAlamat()"
                   placeholder="Cth: Jl. Jend. Sudirman No. 16, RT 01 / RW 02" 
                   class="h-12 px-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm">
        </div>

        <!-- Alamat Lengkap Hasil Gabungan -->
        <div class="flex flex-col gap-2 md:col-span-2">
            <label class="text-sm font-semibold text-gray-700" for="alamat">Pratinjau Alamat Lengkap <span class="text-red-500">*</span></label>
            <textarea class="p-3 border border-gray-200 rounded-xl bg-blue-50/40 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm resize-none font-medium" 
                      id="alamat" name="alamat" placeholder="Pilih Kecamatan dan Desa di atas untuk menghasilkan alamat lengkap..." required="" rows="2" readonly></textarea>
            <p class="text-[11px] text-gray-400">Alamat lengkap otomatis terkomposisi dari pilihan wilayah Kabupaten Ciamis dan rincian jalan di atas.</p>
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
            <input class="h-12 px-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm" 
                   id="subjek" name="subjek_informasi" placeholder="Masukkan judul informasi (Cth: Dokumen RPJMD Kabupaten Ciamis 2021-2026)" required="" type="text">
        </div>
        <!-- Isi Rincian Informasi -->
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <label class="text-sm font-semibold text-gray-700" for="deskripsi">Isi / Uraian Rincian Informasi <span class="text-red-500">*</span></label>
                <span class="text-[11px] text-gray-400">Deskripsi Lengkap</span>
            </div>
            <textarea class="p-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm resize-none" 
                      id="deskripsi" name="rincian_informasi" placeholder="Jelaskan secara rinci bab, tabel, data statistik, atau informasi spesifik yang Anda butuhkan..." required="" rows="4"></textarea>
        </div>
        <!-- Tujuan Penggunaan -->
        <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-gray-700" for="tujuan">Tujuan Penggunaan Informasi <span class="text-red-500">*</span></label>
            <textarea class="p-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-gray-800 placeholder:text-gray-400 text-sm resize-none" id="tujuan" name="tujuan_penggunaan" placeholder="Jelaskan untuk apa informasi ini akan digunakan (Cth: Penelitian Akademis / Penyusunan Skripsi)" required="" rows="3"></textarea>
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
            <input class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500" name="cara_memperoleh_informasi_id" type="radio" value="{{ $cara->id }}" required>
            <div class="flex flex-col">
                <span class="text-sm font-semibold text-gray-800">{{ $cara->nama_cara }}</span>
                <span class="text-xs text-gray-500 mt-0.5">{{ $cara->deskripsi }}</span>
            </div>
        </label>
        @endforeach
    </div>
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
            <p class="text-xs text-gray-500 mt-1">Unggah foto/scan KTP yang jelas. Format didukung: JPG, PNG, PDF (Maks. 5MB).</p>
        </div>
        <div x-data="fileUpload()" 
             @dragover.prevent="dragover = true" 
             @dragleave.prevent="dragover = false" 
             @drop.prevent="handleDrop"
             :class="dragover ? 'border-blue-500 bg-blue-50' : (error ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:bg-blue-50/50 hover:border-blue-300')"
             class="border-2 border-dashed rounded-xl p-8 flex flex-col items-center justify-center text-center transition-colors relative group">
            
            <span class="material-symbols-outlined text-[40px] mb-4 transition-colors" 
                  :class="error ? 'text-red-500' : (dragover || file ? 'text-blue-500' : 'text-gray-400 group-hover:text-blue-500')" 
                  x-text="error ? 'error' : (file ? 'task' : 'cloud_upload')">cloud_upload</span>
            
            <input type="file" id="dokumenKtp" name="file_identitas" :required="!file" accept=".jpg,.jpeg,.png,.pdf" 
                   @change="handleFileChange" class="hidden" x-ref="fileInput">
            
            <div x-show="!file && !error">
                <button type="button" @click="$refs.fileInput.click()" class="text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                    Klik untuk unggah
                </button>
                <span class="text-sm text-gray-500"> atau seret dan lepas file di sini</span>
            </div>

            <div x-show="file && !error" class="flex flex-col items-center" style="display: none;">
                <p class="text-sm font-semibold text-gray-700" x-text="file?.name"></p>
                <p class="text-xs text-gray-500 mt-1" x-text="fileSize"></p>
                <button type="button" @click="resetFile()" class="mt-3 text-xs font-semibold text-red-500 hover:text-red-600">
                    Hapus File
                </button>
            </div>

            <div x-show="error" class="flex flex-col items-center" style="display: none;">
                <p class="text-sm font-semibold text-red-600" x-text="errorMessage"></p>
                <button type="button" @click="resetFile()" class="mt-3 text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Pilih File Lain
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Disclaimer & Actions -->
<div class="pt-6">
    <div class="flex items-start gap-3 mb-8 bg-blue-50/80 p-5 rounded-xl border border-blue-100">
        <span class="material-symbols-outlined text-blue-600 text-[20px] mt-0.5" data-icon="info">info</span>
        <p class="text-sm text-blue-900 leading-relaxed">
            <strong>Pemberitahuan Privasi:</strong> Informasi yang Anda berikan akan dilindungi kerahasiaannya sesuai peraturan yang berlaku dan hanya digunakan untuk keperluan pemrosesan permohonan ini.
        </p>
    </div>
    <div class="flex flex-col-reverse md:flex-row justify-end items-center gap-4">
        <a href="{{ route('home') }}" class="w-full md:w-auto px-6 py-3 rounded-xl text-sm font-semibold text-gray-600 bg-white hover:bg-gray-50 transition-colors border border-gray-200 text-center" type="button">
            Batal
        </a>
        <button class="w-full md:w-auto px-8 py-3 rounded-xl text-sm font-semibold text-white bg-[#03224d] hover:bg-[#0B1B3D] transition-colors shadow-[0_8px_16px_-4px_rgba(3,34,77,0.3)] hover:shadow-lg hover:-translate-y-0.5 text-center" type="submit">
            Kirim Permohonan
        </button>
    </div>
</div>
</form>
</div>
</main>

<script>
document.addEventListener('alpine:init', () => {
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

            const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (!validTypes.includes(selectedFile.type)) {
                this.error = true;
                this.errorMessage = 'Format file tidak diizinkan. Harap unggah JPG, PNG, atau PDF.';
                this.$refs.fileInput.value = '';
                this.file = null;
                return;
            }

            if (selectedFile.size > maxSize) {
                this.error = true;
                this.errorMessage = 'Ukuran file melebihi batas maksimal (5MB).';
                this.$refs.fileInput.value = '';
                this.file = null;
                return;
            }

            // Sync with input file so form submission works properly for dragged files
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(selectedFile);
            this.$refs.fileInput.files = dataTransfer.files;

            this.file = selectedFile;
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
