@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="mb-5">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.surat-keluar.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors flex items-center">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            </a>
            <h2 class="text-lg font-bold text-gray-800 m-0">Buat Draft Surat Keluar</h2>
        </div>
        <p class="text-xs text-gray-500 ml-7">Masukkan rincian untuk draft surat keluar yang baru.</p>
    </div>

    <div class="max-w-3xl bg-white border border-gray-200 rounded shadow-sm p-6">
        <form action="{{ route('admin.surat-keluar.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Tujuan Surat <span class="text-red-500">*</span></label>
                <input type="text" name="tujuan" value="{{ old('tujuan') }}" required placeholder="Contoh: Sekretaris Daerah Kab. Ciamis" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
            </div>

            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Klasifikasi Arsip (Opsional)</label>
                <select name="klasifikasi_arsip_id" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <option value="">-- Pilih Kode Klasifikasi --</option>
                    @foreach($klasifikasiArsips as $ka)
                        <option value="{{ $ka->id }}" {{ old('klasifikasi_arsip_id') == $ka->id ? 'selected' : '' }}>
                            {{ $ka->kode }} - {{ $ka->nama_klasifikasi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Perihal / Ringkasan Isi <span class="text-red-500">*</span></label>
                <textarea name="perihal" rows="3" required placeholder="Contoh: Undangan Rapat Koordinasi" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">{{ old('perihal') }}</textarea>
            </div>

            <div class="mb-5">
                <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">File Draft (.doc / .docx / .pdf)</label>
                <input type="file" name="file_draft" accept=".pdf,.doc,.docx" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                <p class="mt-1 text-[11px] text-gray-500">Unggah draft surat untuk direview atasan sebelum diberi nomor dan TTE.</p>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="bg-[#1a2b42] text-white text-xs font-semibold px-4 py-2 rounded hover:bg-[#121c2e] transition-colors shadow-sm">
                    Simpan Draft
                </button>
                <a href="{{ route('admin.surat-keluar.index') }}" class="bg-white border border-gray-300 text-gray-700 text-xs font-semibold px-4 py-2 rounded hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
