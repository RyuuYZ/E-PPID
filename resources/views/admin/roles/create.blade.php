@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-gray-800 m-0">Tambah Role</h2>
            <p class="text-xs text-gray-500 mt-0.5">Buat role baru dan tentukan hak akses moduler-nya.</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-1.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded px-3 py-1.5 hover:bg-gray-50 transition-colors shadow-sm text-xs">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded shadow-sm text-sm">
            <strong class="font-bold">Terjadi Kesalahan!</strong>
            <ul class="mt-2 list-disc list-inside text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded shadow-sm p-6">
        <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="flex flex-col gap-1.5">
                <label for="name" class="text-xs font-semibold text-gray-700">Nama Role <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3 max-w-md" required placeholder="Cth: Editor">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="description" class="text-xs font-semibold text-gray-700">Deskripsi Role</label>
                <textarea id="description" name="description" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow p-3 max-w-xl" rows="3" placeholder="Penjelasan singkat mengenai fungsi role ini...">{{ old('description') }}</textarea>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <label class="text-sm font-bold text-gray-800 block mb-3">Hak Akses (Permissions)</label>
                <p class="text-xs text-gray-500 mb-4">Centang modul mana saja yang dapat diakses oleh role ini:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($availablePermissions as $key => $label)
                    <label class="flex items-start gap-2.5 p-3 border border-gray-200 rounded hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-[#1a2b42] has-[:checked]:bg-[#f4f6f9]">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}" class="mt-0.5 rounded text-[#1a2b42] focus:ring-[#1a2b42] border-gray-300 h-4 w-4" 
                        {{ is_array(old('permissions')) && in_array($key, old('permissions')) ? 'checked' : '' }}>
                        <span class="text-xs font-medium text-gray-700 leading-snug">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('admin.roles.index') }}" class="inline-flex justify-center rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#1a2b42] focus:ring-offset-2 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center rounded bg-[#1a2b42] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#121c2e] focus:outline-none focus:ring-2 focus:ring-[#1a2b42] focus:ring-offset-2 transition-colors">
                    Simpan Role
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
