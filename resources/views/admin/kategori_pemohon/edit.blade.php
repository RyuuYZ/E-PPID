@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800 m-0">Edit Kategori Pemohon</h2>
        <p class="text-xs text-gray-500 mt-0.5">Ubah data kategori pemohon.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden max-w-2xl">
        <form action="{{ route('admin.kategori-pemohon.update', $kategori->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-5">
                <label for="nama_kategori" class="block text-[13px] font-semibold text-gray-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kategori" id="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors" required>
                @error('nama_kategori')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="bg-[#1a2b42] text-white text-xs font-semibold px-4 py-2 rounded hover:bg-[#121c2e] transition-colors shadow-sm">
                    Perbarui Data
                </button>
                <a href="{{ route('admin.kategori-pemohon.index') }}" class="bg-white border border-gray-300 text-gray-700 text-xs font-semibold px-4 py-2 rounded hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
