@extends('admin.layouts.app')

@section('title', 'Manajemen Hak Akses (Role) - Admin E-PPID')

@section('content')
<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen" 
      x-data="roleManagement({{ Js::from($roles) }}, {{ Js::from($availablePermissions) }})">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 m-0 tracking-tight">Pengaturan Hak Akses (Role)</h1>
            </div>
            <p class="text-xs text-slate-500 ml-10">Kelola role dan konfigurasi izin (permissions) yang dapat diakses oleh setiap role.</p>
        </div>

        <button type="button" @click="openCreateModal()" 
                class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-indigo-700 transition-colors shadow-xs cursor-pointer shrink-0">
            <span class="material-symbols-outlined text-[16px]">add_moderator</span>
            Tambah Role Baru
        </button>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <div class="flex-1">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-3 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-medium shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
            <div class="flex-1">{{ session('error') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs shadow-2xs">
            <div class="flex items-center gap-2 font-bold mb-1">
                <span class="material-symbols-outlined text-[18px] text-rose-600">warning</span>
                Terdapat kesalahan pada formulir:
            </div>
            <ul class="list-disc pl-7 space-y-1 text-rose-700 opacity-90">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Roles Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-for="role in roles" :key="role.id">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow relative flex flex-col h-full group">
                
                <!-- Action Menu (Top Right) -->
                <div x-data="{ open: false }" class="absolute top-4 right-4 text-left" @click.away="open = false" x-show="role.name !== 'Super Admin'">
                    <button @click="open = !open" type="button" 
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors focus:outline-none">
                        <span class="material-symbols-outlined text-[18px]">more_vert</span>
                    </button>
                    
                    <div x-show="open" x-transition style="display: none;"
                         class="origin-top-right absolute right-0 mt-2 w-40 rounded-xl shadow-lg shadow-slate-200/50 bg-white ring-1 ring-black ring-opacity-5 z-20 divide-y divide-slate-100 overflow-hidden">
                        
                        <div class="py-1">
                            <button @click="openEditModal(role); open = false" 
                                    class="group flex w-full items-center px-4 py-2 text-xs text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                <span class="material-symbols-outlined text-[16px] mr-2 text-slate-400 group-hover:text-indigo-600">edit</span> 
                                Edit Role
                            </button>
                        </div>
                        <div class="py-1">
                            <button @click="confirmDelete(role); open = false" 
                                    class="group flex w-full items-center px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 transition-colors">
                                <span class="material-symbols-outlined text-[16px] mr-2 text-rose-500">delete</span> 
                                Hapus Role
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Role Header -->
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border"
                         :class="role.name === 'Super Admin' ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-slate-50 text-slate-600 border-slate-100'">
                        <span class="material-symbols-outlined text-[20px]" x-text="role.name === 'Super Admin' ? 'local_police' : 'badge'"></span>
                    </div>
                    <div class="pr-8">
                        <h3 class="text-sm font-bold text-slate-900" x-text="role.name"></h3>
                        <div class="flex items-center gap-1.5 mt-1 text-[11px] text-slate-500 font-medium">
                            <span class="material-symbols-outlined text-[14px]">groups</span>
                            <span x-text="`${role.users_count || 0} Pengguna`"></span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <p class="text-xs text-slate-600 leading-relaxed min-h-[3rem]" x-text="role.description || 'Tidak ada deskripsi untuk role ini.'"></p>
                </div>

                <!-- Permissions / Chips -->
                <div class="mt-auto pt-4 border-t border-slate-100">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2.5">Hak Akses Modul:</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-if="role.name === 'Super Admin'">
                            <span class="inline-flex items-center px-2 py-1 rounded bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-100">
                                Full Access (Semua Modul)
                            </span>
                        </template>
                        <template x-if="role.name !== 'Super Admin' && (!role.permissions || role.permissions.length === 0)">
                            <span class="text-xs text-slate-400 italic">Belum ada hak akses yang diberikan.</span>
                        </template>
                        <template x-if="role.name !== 'Super Admin' && role.permissions && role.permissions.length > 0">
                            <template x-for="perm in role.permissions" :key="perm">
                                <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 text-slate-700 text-[10px] font-medium border border-slate-200"
                                      x-text="availablePermissions[perm] || perm">
                                </span>
                            </template>
                        </template>
                    </div>
                </div>

            </div>
        </template>
    </div>

    <!-- Modals -->

    <!-- Form Modal (Create & Edit) -->
    <div x-show="formModalOpen" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="formModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="formModalOpen" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-100" @click.away="closeFormModal()">
                    
                    <form :action="formAction" method="POST">
                        @csrf
                        <template x-if="formMode === 'edit'">
                            @method('PUT')
                        </template>

                        <div class="bg-white px-6 pb-6 pt-6">
                            <div class="flex items-center gap-3 mb-5 border-b border-slate-100 pb-4">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined" x-text="formMode === 'create' ? 'add_moderator' : 'edit'"></span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 leading-tight" x-text="formMode === 'create' ? 'Tambah Role Baru' : 'Edit Role'"></h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Tentukan nama role dan pilih izin akses yang sesuai.</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- Nama Role -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Nama Role <span class="text-rose-500">*</span></label>
                                    <input type="text" name="name" x-model="formData.name" required class="w-full text-xs border-slate-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-20 px-3 py-2 bg-slate-50 focus:bg-white transition-colors" placeholder="Cth: Desk Layanan, Tim Penyelesaian Sengketa">
                                </div>

                                <!-- Deskripsi -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Deskripsi Role</label>
                                    <textarea name="description" x-model="formData.description" rows="2" class="w-full text-xs border-slate-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-20 px-3 py-2 bg-slate-50 focus:bg-white transition-colors" placeholder="Penjelasan singkat mengenai peran dan tanggung jawab role ini..."></textarea>
                                </div>

                                <!-- Permissions Selection -->
                                <div class="pt-2 border-t border-slate-100 mt-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block">Hak Akses Modul (Permissions)</label>
                                            <p class="text-[10px] text-slate-500">Pilih modul apa saja yang dapat dikelola oleh role ini.</p>
                                        </div>
                                        <button type="button" @click="toggleAllPermissions()" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded hover:bg-indigo-100 transition-colors">
                                            <span x-text="areAllPermissionsChecked() ? 'Hapus Semua' : 'Pilih Semua'"></span>
                                        </button>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2 max-h-60 overflow-y-auto pr-2">
                                        <template x-for="(label, key) in availablePermissions" :key="key">
                                            <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors"
                                                   :class="formData.permissions.includes(key) ? 'bg-indigo-50/50 border-indigo-200' : 'bg-white border-slate-200 hover:border-indigo-300'">
                                                <div class="flex items-center h-5">
                                                    <input type="checkbox" name="permissions[]" :value="key" 
                                                           x-model="formData.permissions"
                                                           class="w-4 h-4 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500 focus:ring-2 transition-colors">
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold text-slate-700 leading-tight" x-text="label"></span>
                                                    <span class="text-[10px] text-slate-500 mt-0.5" x-text="getPermissionDescription(key)"></span>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 rounded-b-2xl border-t border-slate-100">
                            <button type="button" @click="closeFormModal()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                                <span x-text="formMode === 'create' ? 'Simpan Role' : 'Simpan Perubahan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Dialog -->
    <div x-show="confirmModalOpen" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="confirmModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="confirmModalOpen" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-slate-100" @click.away="confirmModalOpen = false">
                    
                    <div class="bg-white px-6 pb-6 pt-6">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center mb-4 bg-rose-100 text-rose-600">
                                <span class="material-symbols-outlined text-[28px]">warning</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Hapus Role</h3>
                            <p class="text-xs text-slate-500 leading-relaxed" x-text="confirmMessage"></p>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-between gap-3 rounded-b-2xl border-t border-slate-100">
                        <button type="button" @click="confirmModalOpen = false" class="w-1/2 px-4 py-2.5 text-xs font-bold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <form :action="confirmUrl" method="POST" class="w-1/2 m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2.5 text-xs font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors shadow-sm">
                                Ya, Hapus Role
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('roleManagement', (initialRoles, availablePermissions) => ({
        roles: initialRoles,
        availablePermissions: availablePermissions,
        
        // Modal states
        formModalOpen: false,
        formMode: 'create', // create, edit
        formAction: '',
        formData: {
            id: '', name: '', description: '', permissions: []
        },
        
        confirmModalOpen: false,
        confirmUrl: '',
        confirmMessage: '',
        
        // Modals
        openCreateModal() {
            this.formMode = 'create';
            this.formAction = "{{ route('admin.roles.store') }}";
            this.formData = { id: '', name: '', description: '', permissions: [] };
            this.formModalOpen = true;
        },
        openEditModal(role) {
            this.formMode = 'edit';
            this.formAction = `/admin/roles/${role.id}`;
            this.formData = { 
                id: role.id, 
                name: role.name, 
                description: role.description || '', 
                permissions: role.permissions || [] 
            };
            this.formModalOpen = true;
        },
        closeFormModal() {
            this.formModalOpen = false;
        },
        
        confirmDelete(role) {
            if (role.users_count > 0) {
                alert(`Tidak dapat menghapus role "${role.name}" karena masih digunakan oleh ${role.users_count} pengguna.`);
                return;
            }
            
            this.confirmUrl = `/admin/roles/${role.id}`;
            this.confirmMessage = `Apakah Anda yakin ingin menghapus role "${role.name}"? Aksi ini tidak dapat dibatalkan.`;
            this.confirmModalOpen = true;
        },

        // Helper
        toggleAllPermissions() {
            if (this.areAllPermissionsChecked()) {
                this.formData.permissions = [];
            } else {
                this.formData.permissions = Object.keys(this.availablePermissions);
            }
        },
        areAllPermissionsChecked() {
            return this.formData.permissions.length === Object.keys(this.availablePermissions).length;
        },
        getPermissionDescription(key) {
            const descriptions = {
                'manage_users': 'Hak untuk menambah, mengubah, memblokir dan menghapus pengguna.',
                'manage_roles': 'Hak untuk mengatur peran pengguna dan izin sistem.',
                'manage_settings': 'Hak untuk mengubah konfigurasi dan pengaturan dasar E-PPID.',
                'manage_logs': 'Hak untuk melihat log aktivitas pengguna dan error.',
                'manage_master_data': 'Hak untuk mengelola referensi kategori, format, dan bidang.',
                'manage_persuratan': 'Hak untuk mencatat dan mendisposisikan surat masuk/keluar.',
                'manage_permohonan': 'Hak untuk memproses tiket layanan permohonan informasi publik.'
            };
            return descriptions[key] || 'Memberikan akses pada modul terkait.';
        }
    }));
});
</script>
@endsection
