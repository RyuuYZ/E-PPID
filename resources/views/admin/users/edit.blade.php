@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-gray-800 m-0">Edit Pengguna</h2>
            <p class="text-xs text-gray-500 mt-0.5">Perbarui detail akun dan hak akses pengguna.</p>
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
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Nama Lengkap -->
                <div class="flex flex-col gap-1.5">
                    <label for="name" class="text-xs font-semibold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" required>
                </div>

                <!-- Email -->
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-xs font-semibold text-gray-700">Alamat Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" required>
                </div>

                <!-- Password (Opsional) -->
                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-xs font-semibold text-gray-700">Kata Sandi Baru</label>
                    <input type="password" id="password" name="password" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" minlength="8" placeholder="(Kosongkan jika tidak ingin mengubah)">
                </div>

                <!-- Konfirmasi Password -->
                <div class="flex flex-col gap-1.5">
                    <label for="password_confirmation" class="text-xs font-semibold text-gray-700">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full text-sm border-gray-300 rounded focus:border-[#1a2b42] focus:ring focus:ring-[#1a2b42] focus:ring-opacity-20 transition-shadow h-10 px-3" minlength="8" placeholder="Ulangi kata sandi baru">
                </div>

                <!-- Hak Akses (Role) -->
                <div class="md:col-span-2 mt-4 border-t border-gray-100 pt-6">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-1 h-5 bg-[#1a2b42] rounded-sm"></div>
                        <h3 class="text-base font-bold text-gray-800 m-0">Role & Akses</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Pilih hak akses (role) utama untuk pengguna ini. Role menentukan fitur apa saja yang dapat diakses.</p>
                    
                    @if(isset($hasSuperAdmin) && $hasSuperAdmin)
                        <p class="text-xs text-orange-600 mb-3 font-medium">Role Super Admin sudah dipakai. Sistem hanya mengizinkan 1 akun Super Admin.</p>
                    @endif

                    <div class="grid grid-cols-1 gap-3">
                        @foreach($roles as $role)
                            @php
                                $permCount = is_array($role->permissions) ? count($role->permissions) : 0;
                                $isSuperAdmin = isset($superAdminRole) && $role->id == $superAdminRole->id;
                                $isDisabled = $isSuperAdmin && $hasSuperAdmin;
                            @endphp
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none 
                                {{ old('role_id', $user->role_id) == $role->id ? 'border-[#1a2b42] ring-1 ring-[#1a2b42]' : 'border-gray-200 hover:bg-gray-50' }}
                                {{ $isDisabled ? 'opacity-50 cursor-not-allowed bg-gray-50' : '' }}">
                                <input type="radio" name="role_id" value="{{ $role->id }}" class="sr-only" required {{ old('role_id', $user->role_id) == $role->id ? 'checked' : '' }} {{ $isDisabled ? 'disabled' : '' }} onchange="updateRoleUI(this)">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">{{ $role->name }}</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">{{ $role->description ?? 'Tidak ada deskripsi.' }}</span>
                                    </span>
                                </span>
                                <span class="ml-4 flex items-center text-xs text-gray-400">
                                    {{ $permCount }} permissions
                                </span>
                                <span class="pointer-events-none absolute -inset-px rounded-lg border-2 border-transparent" aria-hidden="true"></span>
                            </label>
                        @endforeach
                    </div>
                    @error('role_id') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                    
                    <script>
                        function updateRoleUI(radio) {
                            // Reset all labels
                            document.querySelectorAll('input[name="role_id"]').forEach((input) => {
                                const label = input.closest('label');
                                label.classList.remove('border-[#1a2b42]', 'ring-1', 'ring-[#1a2b42]');
                                label.classList.add('border-gray-200', 'hover:bg-gray-50');
                            });
                            
                            // Highlight selected label
                            if (radio.checked) {
                                const selectedLabel = radio.closest('label');
                                selectedLabel.classList.remove('border-gray-200', 'hover:bg-gray-50');
                                selectedLabel.classList.add('border-[#1a2b42]', 'ring-1', 'ring-[#1a2b42]');

                                // Show/hide unit pengolah dropdown based on role
                                const roleName = selectedLabel.querySelector('.text-sm').textContent.trim();
                                const unitPengolahContainer = document.getElementById('unit-pengolah-container');
                                if (roleName === 'Petugas Penghubung') {
                                    unitPengolahContainer.style.display = 'block';
                                    document.getElementById('unit_pengolah_id').required = true;
                                } else {
                                    unitPengolahContainer.style.display = 'none';
                                    const upEl = document.getElementById('unit_pengolah_id');
                                    upEl.required = false;
                                    if (upEl.tomselect) {
                                        upEl.tomselect.clear();
                                    } else {
                                        upEl.value = '';
                                    }
                                }
                            }
                        }

                        // Initialize on load
                        document.addEventListener('DOMContentLoaded', () => {
                            const checkedRadio = document.querySelector('input[name="role_id"]:checked');
                            if (checkedRadio) updateRoleUI(checkedRadio);
                        });
                    </script>
                </div>

                <!-- Unit Pengolah (Khusus Petugas Penghubung) -->
                <div id="unit-pengolah-container" class="md:col-span-2 mt-4 pt-4 border-t border-gray-100" style="display: none;">
                    <div class="flex flex-col gap-1.5">
                        <label for="unit_pengolah_id" class="text-xs font-semibold text-gray-700">Unit Pengolah / Bidang <span class="text-red-500">*</span></label>
                        <p class="text-[10px] text-gray-500 mb-1">Khusus untuk role "Petugas Penghubung", wajib memilih asal unit pengolah/bidang.</p>
                        <select id="unit_pengolah_id" name="unit_pengolah_id" data-placeholder="-- Pilih Unit Pengolah --" data-search-placeholder="Cari unit pengolah..." class="custom-select w-full text-xs font-medium">
                            <option value="">-- Pilih Unit Pengolah --</option>
                            @foreach($unitPengolahs as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_pengolah_id', $user->unit_pengolah_id) == $unit->id ? 'selected' : '' }}>{{ $unit->nama_bidang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Status Blokir -->
                <div class="flex items-center gap-2 mt-2 md:col-span-2 bg-gray-50 p-3 rounded border border-gray-200">
                    <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded text-[#1a2b42] focus:ring-[#1a2b42] border-gray-300 h-4 w-4" {{ old('is_active', $user->is_active) ? 'checked' : '' }} {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                    <label for="is_active" class="text-sm font-semibold text-gray-700">Akun Aktif</label>
                    <span class="text-xs text-gray-500 ml-2">(Hapus centang untuk memblokir login akun ini)</span>
                    @if($user->id === auth()->id())
                    <span class="text-xs text-red-500 ml-2">(Anda tidak dapat memblokir diri sendiri)</span>
                    <input type="hidden" name="is_active" value="1">
                    @endif
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="inline-flex justify-center rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#1a2b42] focus:ring-offset-2 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center rounded bg-[#1a2b42] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#121c2e] focus:outline-none focus:ring-2 focus:ring-[#1a2b42] focus:ring-offset-2 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
