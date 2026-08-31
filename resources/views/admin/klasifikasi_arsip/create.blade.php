@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="mb-5">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.klasifikasi-arsip.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors flex items-center">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            </a>
            <h2 class="text-lg font-bold text-gray-800 m-0">Tambah Kode Klasifikasi</h2>
        </div>
    </div>

    <div class="max-w-2xl bg-white border border-gray-200 rounded shadow-sm p-6">
        <form action="{{ route('admin.klasifikasi-arsip.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Kode (Misal: 000) <span class="text-red-500">*</span></label>
                <input type="text" name="kode" value="{{ old('kode') }}" required class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                @error('kode') <span class="text-[11px] font-medium text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Nama Klasifikasi (Misal: Umum) <span class="text-red-500">*</span></label>
                <input type="text" name="nama_klasifikasi" value="{{ old('nama_klasifikasi') }}" required class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                @error('nama_klasifikasi') <span class="text-[11px] font-medium text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Keterangan (Opsional)</label>
                <textarea name="keterangan" rows="3" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="bg-[#1a2b42] text-white text-xs font-semibold px-4 py-2 rounded hover:bg-[#121c2e] transition-colors shadow-sm">
                    Simpan
                </button>
                <a href="{{ route('admin.klasifikasi-arsip.index') }}" class="bg-white border border-gray-300 text-gray-700 text-xs font-semibold px-4 py-2 rounded hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
