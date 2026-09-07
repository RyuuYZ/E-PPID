@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="mb-5">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.surat-masuk.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors flex items-center">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            </a>
            <h2 class="text-lg font-bold text-gray-800 m-0">Registrasi Surat Masuk</h2>
        </div>
        <p class="text-xs text-gray-500 ml-7">Masukkan data detail surat masuk yang baru diterima.</p>
    </div>

    <div class="max-w-3xl bg-white border border-gray-200 rounded shadow-sm overflow-hidden p-6">
        <form action="{{ route('admin.surat-masuk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Nomor Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" required class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Asal/Pengirim Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="pengirim" value="{{ old('pengirim') }}" required class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat') }}" required class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Tanggal Diterima Bappeda <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_diterima" value="{{ old('tanggal_diterima', date('Y-m-d')) }}" required class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Klasifikasi Arsip (Opsional)</label>
                <select name="klasifikasi_arsip_id" data-placeholder="-- Pilih Kode Klasifikasi --" data-search-placeholder="Cari kode atau klasifikasi..." class="custom-select w-full text-xs font-medium">
                    <option value="">-- Pilih Kode Klasifikasi --</option>
                    @foreach($klasifikasiArsips as $ka)
                        <option value="{{ $ka->id }}" {{ old('klasifikasi_arsip_id') == $ka->id ? 'selected' : '' }}>
                            {{ $ka->kode }} - {{ $ka->nama_klasifikasi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Perihal / Ringkasan <span class="text-red-500">*</span></label>
                <textarea name="perihal" rows="3" required class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">{{ old('perihal') }}</textarea>
            </div>

            <div class="mb-5">
                <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Scan File Lampiran (Opsional)</label>
                <input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                <p class="mt-1 text-xs text-gray-500">Maks. ukuran file 5MB (PDF/JPG/PNG).</p>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="bg-[#1a2b42] text-white text-xs font-semibold px-4 py-2 rounded hover:bg-[#121c2e] transition-colors shadow-sm">
                    Simpan Registrasi
                </button>
                <a href="{{ route('admin.surat-masuk.index') }}" class="bg-white border border-gray-300 text-gray-700 text-xs font-semibold px-4 py-2 rounded hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
