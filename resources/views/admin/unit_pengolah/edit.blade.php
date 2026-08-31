@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="mb-5">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.unit-pengolah.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors flex items-center">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            </a>
            <h2 class="text-lg font-bold text-gray-800 m-0">Edit Bidang</h2>
        </div>
        <p class="text-[11px] text-gray-500 ml-7">Perbarui data Unit Pengolah Data.</p>
    </div>

    <div class="max-w-2xl bg-white border border-gray-200 rounded shadow-sm p-6">
        <form action="{{ route('admin.unit-pengolah.update', $unitPengolah->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="nama_bidang" class="block text-[13px] font-semibold text-gray-700 mb-1.5">Nama Bidang <span class="text-red-500">*</span></label>
                <input type="text" id="nama_bidang" name="nama_bidang" value="{{ old('nama_bidang', $unitPengolah->nama_bidang) }}" required class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                @error('nama_bidang')
                    <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="deskripsi" class="block text-[13px] font-semibold text-gray-700 mb-1.5">Deskripsi / Keterangan</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">{{ old('deskripsi', $unitPengolah->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="bg-[#1a2b42] text-white text-xs font-semibold px-4 py-2 rounded hover:bg-[#121c2e] transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.unit-pengolah.index') }}" class="bg-white border border-gray-300 text-gray-700 text-xs font-semibold px-4 py-2 rounded hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
