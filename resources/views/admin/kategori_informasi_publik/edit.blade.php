@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800 m-0">Edit Kategori Informasi Publik</h2>
        <p class="text-xs text-gray-500 mt-0.5">Ubah data kategori ketersediaan informasi publik.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden max-w-2xl">
        <form action="{{ route('admin.kategori-informasi-publik.update', $kategori->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="nama_kategori" class="block text-[13px] font-semibold text-gray-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kategori" id="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors" required>
                @error('nama_kategori')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="icon" class="block text-[13px] font-semibold text-gray-700 mb-1.5">Nama Ikon (Material)</label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon', $kategori->icon) }}" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
                <div>
                    <label for="bg_color" class="block text-[13px] font-semibold text-gray-700 mb-1.5">Warna Latar</label>
                    <input type="text" name="bg_color" id="bg_color" value="{{ old('bg_color', $kategori->bg_color) }}" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
                <div>
                    <label for="text_color" class="block text-[13px] font-semibold text-gray-700 mb-1.5">Warna Teks</label>
                    <input type="text" name="text_color" id="text_color" value="{{ old('text_color', $kategori->text_color) }}" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
            </div>

            <div class="mb-5">
                <label for="deskripsi" class="block text-[13px] font-semibold text-gray-700 mb-1.5">Deskripsi Singkat</label>
                <textarea name="deskripsi" id="deskripsi" rows="2" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="bg-[#1a2b42] text-white text-xs font-semibold px-4 py-2 rounded hover:bg-[#121c2e] transition-colors shadow-sm">
                    Perbarui Data
                </button>
                <a href="{{ route('admin.kategori-informasi-publik.index') }}" class="bg-white border border-gray-300 text-gray-700 text-xs font-semibold px-4 py-2 rounded hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
