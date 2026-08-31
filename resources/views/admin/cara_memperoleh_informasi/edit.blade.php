@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="mb-5">
        <h2 class="text-lg font-bold text-gray-800 m-0">Edit Cara Memperoleh Informasi</h2>
        <p class="text-xs text-gray-500 mt-0.5">Ubah data cara pemohon mendapatkan informasi.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden max-w-2xl">
        <form action="{{ route('admin.cara-memperoleh-informasi.update', $cara->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="nama_cara" class="block text-[13px] font-semibold text-gray-700 mb-1.5">Nama Cara <span class="text-red-500">*</span></label>
                <input type="text" name="nama_cara" id="nama_cara" value="{{ old('nama_cara', $cara->nama_cara) }}" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors" required>
                @error('nama_cara')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="deskripsi" class="block text-[13px] font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">{{ old('deskripsi', $cara->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="bg-[#1a2b42] text-white text-xs font-semibold px-4 py-2 rounded hover:bg-[#121c2e] transition-colors shadow-sm">
                    Perbarui Data
                </button>
                <a href="{{ route('admin.cara-memperoleh-informasi.index') }}" class="bg-white border border-gray-300 text-gray-700 text-xs font-semibold px-4 py-2 rounded hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
