@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-gray-800 m-0">Tambah Pengguna</h2>
            <p class="text-xs text-gray-500 mt-0.5">Buat akun pengguna baru dalam sistem.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded px-3 py-1.5 hover:bg-gray-50 transition-colors shadow-sm text-xs">
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
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Nama Lengkap -->
                <div class="flex flex-col gap-1.5">
                    <label for="name" class="text-xs font-semibold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" required placeholder="Masukkan nama pengguna">
                </div>

                <!-- Email -->
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-xs font-semibold text-gray-700">Alamat Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" required placeholder="email@contoh.com">
                </div>

                <!-- Password -->
                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-xs font-semibold text-gray-700">Kata Sandi <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" required minlength="8" placeholder="Minimal 8 karakter">
                </div>

                <!-- Konfirmasi Password -->
                <div class="flex flex-col gap-1.5">
                    <label for="password_confirmation" class="text-xs font-semibold text-gray-700">Konfirmasi Kata Sandi <span class="text-red-500">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" required minlength="8" placeholder="Ulangi kata sandi">
                </div>

                <!-- Hak Akses (Role) -->
                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label for="role_id" class="text-xs font-semibold text-gray-700">Hak Akses (Role) <span class="text-red-500">*</span></label>
                    <select id="role_id" name="role_id" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3 bg-white" required>
                        <option value="" disabled selected>Pilih Role Pengguna</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Blokir -->
                <div class="flex items-center gap-2 mt-2 md:col-span-2 bg-gray-50 p-3 rounded border border-gray-200">
                    <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded text-[#1a2b42] focus:ring-[#1a2b42] border-gray-300 h-4 w-4" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-semibold text-gray-700">Akun Aktif</label>
                    <span class="text-xs text-gray-500 ml-2">(Hapus centang untuk memblokir login akun ini)</span>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="inline-flex justify-center rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#1a2b42] focus:ring-offset-2 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center rounded bg-[#1a2b42] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#121c2e] focus:outline-none focus:ring-2 focus:ring-[#1a2b42] focus:ring-offset-2 transition-colors">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
