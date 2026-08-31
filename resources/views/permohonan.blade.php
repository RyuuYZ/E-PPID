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
<main class="flex-grow py-stack-lg px-margin-mobile md:px-margin-desktop">
<div class="max-w-3xl mx-auto w-full">
<!-- Header Section -->
<div class="mb-stack-lg text-center md:text-left">
<h1 class="font-headline-lg-mobile md:font-display-lg text-headline-lg-mobile md:text-display-lg text-primary mb-stack-sm">
                    Formulir Permohonan Informasi Publik
                </h1>
<p class="font-body-lg text-body-lg text-on-surface-variant">
                    Silakan lengkapi formulir di bawah ini untuk mengajukan permohonan informasi baru.
                </p>
</div>
<!-- Form -->
<form action="{{ route('permohonan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-stack-lg">
@csrf

<!-- Section 1: Data Diri Pemohon -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-gutter shadow-sm transition-shadow hover:shadow-md">
<div class="flex items-center gap-3 mb-stack-md border-b border-surface-variant pb-3">
<span class="material-symbols-outlined text-primary text-[28px]" data-icon="person">person</span>
<h2 class="font-headline-md text-headline-md text-primary">Data Diri Pemohon</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md">
<!-- Nama Lengkap -->
<div class="flex flex-col gap-2 md:col-span-2">
<label class="font-label-md text-label-md text-on-surface" for="namaLengkap">Nama Lengkap <span class="text-error">*</span></label>
<input class="h-12 px-4 border border-outline-variant rounded-DEFAULT bg-surface-container-lowest focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-colors font-body-md text-body-md text-on-surface placeholder:text-outline" id="namaLengkap" name="nama_pemohon" placeholder="Masukkan nama lengkap sesuai identitas" required="" type="text">
</div>
<!-- NIK -->
<div class="flex flex-col gap-2">
<label class="font-label-md text-label-md text-on-surface" for="nik">NIK / No. Identitas <span class="text-error">*</span></label>
<input class="h-12 px-4 border border-outline-variant rounded-DEFAULT bg-surface-container-lowest focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-colors font-body-md text-body-md text-on-surface placeholder:text-outline" id="nik" name="nik_atau_no_badan_hukum" placeholder="16 digit NIK atau Nomor Badan Hukum" required="" type="text">
</div>
<!-- Kategori Pemohon -->
<div class="flex flex-col gap-2 md:col-span-2">
<label class="font-label-md text-label-md text-on-surface" for="kategori_pemohon">Kategori Pemohon <span class="text-error">*</span></label>
<select class="h-12 px-4 border border-outline-variant rounded-DEFAULT bg-surface-container-lowest focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-colors font-body-md text-body-md text-on-surface" id="kategori_pemohon" name="kategori_pemohon_id" required>
    <option value="" disabled selected>Pilih Kategori Pemohon...</option>
    @foreach($kategoriPemohons as $kp)
        <option value="{{ $kp->id }}">{{ $kp->nama_kategori }}</option>
    @endforeach
</select>
</div>
<!-- No. Telepon -->
<div class="flex flex-col gap-2">
<label class="font-label-md text-label-md text-on-surface" for="telepon">No. Telepon / WhatsApp <span class="text-error">*</span></label>
<input class="h-12 px-4 border border-outline-variant rounded-DEFAULT bg-surface-container-lowest focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-colors font-body-md text-body-md text-on-surface placeholder:text-outline" id="telepon" name="no_telp" placeholder="08xxxxxxxxxx" required="" type="tel">
</div>
<!-- Email -->
<div class="flex flex-col gap-2">
<label class="font-label-md text-label-md text-on-surface" for="email">Alamat Email <span class="text-error">*</span></label>
<input class="h-12 px-4 border border-outline-variant rounded-DEFAULT bg-surface-container-lowest focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-colors font-body-md text-body-md text-on-surface placeholder:text-outline" id="email" name="email" placeholder="email@contoh.com" required="" type="email">
</div>
<!-- Alamat Lengkap -->
<div class="flex flex-col gap-2 md:col-span-2">
<label class="font-label-md text-label-md text-on-surface" for="alamat">Alamat Lengkap <span class="text-error">*</span></label>
<textarea class="p-4 border border-outline-variant rounded-DEFAULT bg-surface-container-lowest focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-colors font-body-md text-body-md text-on-surface placeholder:text-outline resize-none" id="alamat" name="alamat" placeholder="Masukkan alamat lengkap (Jalan, RT/RW, Desa/Kelurahan, Kecamatan)" required="" rows="3"></textarea>
</div>
</div>
</section>

<!-- Section 2: Rincian Informasi yang Dibutuhkan -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-gutter shadow-sm transition-shadow hover:shadow-md">
<div class="flex items-center gap-3 mb-stack-md border-b border-surface-variant pb-3">
<span class="material-symbols-outlined text-primary text-[28px]" data-icon="description">description</span>
<h2 class="font-headline-md text-headline-md text-primary">Rincian Informasi yang Dibutuhkan</h2>
</div>
<div class="flex flex-col gap-stack-md">
<!-- Subjek Informasi -->
<div class="flex flex-col gap-2">
<label class="font-label-md text-label-md text-on-surface" for="subjek">Subjek Informasi <span class="text-error">*</span></label>
<input class="h-12 px-4 border border-outline-variant rounded-DEFAULT bg-surface-container-lowest focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-colors font-body-md text-body-md text-on-surface placeholder:text-outline" id="subjek" name="subjek" placeholder="Garis besar informasi yang diminta (Cth: Dokumen APBD 2023)" required="" type="text">
</div>
<!-- Deskripsi Detail -->
<div class="flex flex-col gap-2">
<label class="font-label-md text-label-md text-on-surface" for="deskripsi">Deskripsi Detail Informasi <span class="text-error">*</span></label>
<textarea class="p-4 border border-outline-variant rounded-DEFAULT bg-surface-container-lowest focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-colors font-body-md text-body-md text-on-surface placeholder:text-outline resize-none" id="deskripsi" name="rincian_informasi" placeholder="Jelaskan secara rinci dokumen atau informasi yang Anda butuhkan..." required="" rows="4"></textarea>
</div>
<!-- Tujuan Penggunaan -->
<div class="flex flex-col gap-2">
<label class="font-label-md text-label-md text-on-surface" for="tujuan">Tujuan Penggunaan Informasi <span class="text-error">*</span></label>
<textarea class="p-4 border border-outline-variant rounded-DEFAULT bg-surface-container-lowest focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-colors font-body-md text-body-md text-on-surface placeholder:text-outline resize-none" id="tujuan" name="tujuan_penggunaan" placeholder="Jelaskan untuk apa informasi ini akan digunakan (Cth: Penelitian Akademis)" required="" rows="3"></textarea>
</div>
</div>
</section>

<!-- Section 3: Cara Memperoleh Informasi -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-gutter shadow-sm transition-shadow hover:shadow-md">
<div class="flex items-center gap-3 mb-stack-md border-b border-surface-variant pb-3">
<span class="material-symbols-outlined text-primary text-[28px]" data-icon="inventory_2">inventory_2</span>
<h2 class="font-headline-md text-headline-md text-primary">Cara Memperoleh Informasi</h2>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mb-4">Pilih bagaimana Anda ingin menerima informasi tersebut:</p>
<div class="flex flex-col gap-3">
@foreach($caraMemperoleh as $cara)
<label class="flex items-center gap-3 p-4 border border-outline-variant rounded-DEFAULT cursor-pointer hover:bg-surface-container-low transition-colors has-[:checked]:border-primary-container has-[:checked]:bg-primary-fixed/20">
<input class="w-5 h-5 text-primary-container border-outline-variant focus:ring-primary-container" name="cara_memperoleh_informasi_id" type="radio" value="{{ $cara->id }}" required>
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface">{{ $cara->nama_cara }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">{{ $cara->deskripsi }}</span>
</div>
</label>
@endforeach
</div>
</section>

<!-- Section 4: Unggah Dokumen Pendukung -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-gutter shadow-sm transition-shadow hover:shadow-md">
<div class="flex items-center gap-3 mb-stack-md border-b border-surface-variant pb-3">
<span class="material-symbols-outlined text-primary text-[28px]" data-icon="upload_file">upload_file</span>
<h2 class="font-headline-md text-headline-md text-primary">Unggah Dokumen Pendukung</h2>
</div>
<div class="flex flex-col gap-2">
<label class="font-label-md text-label-md text-on-surface">KTP / Identitas Diri Resmi <span class="text-error">*</span></label>
<p class="font-body-md text-body-md text-on-surface-variant mb-2">Unggah foto/scan KTP yang jelas. Format didukung: JPG, PNG, PDF (Maks. 5MB).</p>
<div class="border-2 border-dashed border-outline-variant rounded-lg p-stack-lg flex flex-col items-center justify-center text-center bg-surface-container-lowest hover:bg-surface-container-low hover:border-primary-container transition-colors relative group">
<span class="material-symbols-outlined text-[48px] text-outline-variant group-hover:text-primary-container mb-4" data-icon="cloud_upload">cloud_upload</span>
<input accept=".jpg,.jpeg,.png,.pdf" class="block w-full max-w-xs text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-container file:text-on-primary hover:file:bg-primary transition-colors cursor-pointer" id="dokumenKtp" name="file_identitas" required="" type="file">
</div>
</div>
</section>

<!-- Disclaimer & Actions -->
<div class="pt-stack-md">
<div class="flex items-start gap-2 mb-stack-md bg-primary-fixed/30 p-4 rounded-lg border border-primary-fixed-dim">
<span class="material-symbols-outlined text-primary text-[20px] mt-0.5" data-icon="info">info</span>
<p class="font-body-md text-body-md text-on-surface">
<strong>Pemberitahuan Privasi:</strong> Informasi yang Anda berikan akan dilindungi kerahasiaannya sesuai peraturan yang berlaku dan hanya digunakan untuk keperluan pemrosesan permohonan ini.
                        </p>
</div>
<div class="flex flex-col-reverse md:flex-row justify-end items-center gap-4">
<a href="{{ route('home') }}" class="w-full md:w-auto px-6 py-3 rounded-lg font-label-md text-label-md font-bold text-on-surface-variant bg-surface-container-highest hover:bg-surface-variant transition-colors border border-outline-variant text-center" type="button">
                            Batal
                        </a>
<button class="w-full md:w-auto px-8 py-3 rounded-lg font-label-md text-label-md font-bold text-on-primary bg-primary-container hover:bg-primary transition-colors shadow-sm hover:shadow-md" type="submit">
                            Kirim Permohonan
                        </button>
</div>
</div>
</form>
</div>
</main>
@endsection
