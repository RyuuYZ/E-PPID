@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna - Admin E-PPID')

@section('content')
<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen" 
      x-data="userManagement({{ Js::from($users) }})">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 m-0 tracking-tight">Manajemen Pengguna</h1>
            </div>
            <p class="text-xs text-slate-500 ml-10">Kelola akun pengguna, peran, unit pengolah, dan status login sistem.</p>
        </div>

        <button type="button" @click="openCreateModal()" 
                class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition-colors shadow-xs cursor-pointer shrink-0">
            <span class="material-symbols-outlined text-[16px]">person_add</span>
            Tambah Pengguna
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

    <!-- Quick Stats / Bento Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <!-- Total -->
        <div @click="filterStatus = 'all'; applyFilters()" 
             class="bg-white p-4 rounded-2xl border cursor-pointer transition-all duration-200 hover:shadow-md"
             :class="filterStatus === 'all' ? 'border-blue-500 shadow-sm ring-1 ring-blue-500' : 'border-slate-200 hover:border-blue-300'">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">groups</span>
                </div>
                <span class="text-2xl font-black text-slate-800">{{ $users->count() }}</span>
            </div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna</p>
        </div>

        <!-- Aktif -->
        <div @click="filterStatus = 'active'; applyFilters()" 
             class="bg-white p-4 rounded-2xl border cursor-pointer transition-all duration-200 hover:shadow-md"
             :class="filterStatus === 'active' ? 'border-emerald-500 shadow-sm ring-1 ring-emerald-500' : 'border-slate-200 hover:border-emerald-300'">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                </div>
                <span class="text-2xl font-black text-slate-800">{{ $users->where('is_active', true)->count() }}</span>
            </div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengguna Aktif</p>
        </div>

        <!-- Diblokir -->
        <div @click="filterStatus = 'blocked'; applyFilters()" 
             class="bg-white p-4 rounded-2xl border cursor-pointer transition-all duration-200 hover:shadow-md"
             :class="filterStatus === 'blocked' ? 'border-rose-500 shadow-sm ring-1 ring-rose-500' : 'border-slate-200 hover:border-rose-300'">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">person_off</span>
                </div>
                <span class="text-2xl font-black text-slate-800">{{ $users->where('is_active', false)->count() }}</span>
            </div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Akun Diblokir</p>
        </div>

        <!-- Perlu Ganti Password -->
        <div @click="filterStatus = 'password_change'; applyFilters()" 
             class="bg-white p-4 rounded-2xl border cursor-pointer transition-all duration-200 hover:shadow-md"
             :class="filterStatus === 'password_change' ? 'border-amber-500 shadow-sm ring-1 ring-amber-500' : 'border-slate-200 hover:border-amber-300'">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">password</span>
                </div>
                <span class="text-2xl font-black text-slate-800">{{ $users->where('must_change_password', true)->count() }}</span>
            </div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Perlu Setup Sandi</p>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="flex flex-col md:flex-row gap-3 mb-4">
        <!-- Search -->
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <span class="material-symbols-outlined text-[18px]">search</span>
            </div>
            <input type="text" x-model="search" @input="applyFilters" placeholder="Cari nama, username, email..." 
                   class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-xs focus:ring-blue-500 focus:border-blue-500 bg-white placeholder-slate-400">
        </div>

        <!-- Filter Role -->
        <div class="w-full md:w-56 shrink-0 relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" type="button" 
                    class="w-full flex items-center justify-between gap-2 px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2 truncate">
                    <span class="material-symbols-outlined text-[16px] text-slate-400">admin_panel_settings</span>
                    <span class="font-medium text-slate-700 truncate" x-text="filterRole === 'all' ? 'Semua Role' : (roles.find(r => r.id === filterRole)?.name || 'Semua Role')">Semua Role</span>
                </div>
                <span class="material-symbols-outlined text-[16px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
            </button>
            
            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;"
                 class="absolute z-20 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg shadow-slate-200/50 py-1 max-h-60 overflow-y-auto">
                <div @click="filterRole = 'all'; applyFilters(); open = false" 
                     class="px-3 py-2 text-xs cursor-pointer transition-colors flex items-center gap-2"
                     :class="filterRole === 'all' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50'">
                    <span class="material-symbols-outlined text-[14px]">all_inclusive</span>
                    Semua Role
                </div>
                <div class="h-px bg-slate-100 my-1"></div>
                <template x-for="r in roles" :key="r.id">
                    <div @click="filterRole = r.id; applyFilters(); open = false" 
                         class="px-3 py-2 text-xs cursor-pointer transition-colors flex items-center gap-2"
                         :class="filterRole === r.id ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50'">
                        <span class="material-symbols-outlined text-[14px]">badge</span>
                        <span x-text="r.name"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-[50px]">No</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-[35%]">Pengguna</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-[30%]">Hak Akses & Unit</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-[15%] text-center">Status</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-[10%] text-center">Keamanan</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-[10%] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <template x-for="(user, index) in paginatedItems" :key="user.id">
                        <tr class="hover:bg-slate-50/50 transition-colors group" :class="!user.is_active ? 'bg-slate-50/80 opacity-70' : ''">
                            <td class="px-5 py-3 text-xs text-slate-400 font-medium whitespace-nowrap" x-text="getStartRecord() + index"></td>
                            
                            <!-- Pengguna -->
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs shrink-0" 
                                         x-text="user.name.charAt(0).toUpperCase()"></div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-800 text-xs truncate" x-text="user.name"></div>
                                        <div class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-1.5 truncate">
                                            <span class="material-symbols-outlined text-[11px]">badge</span>
                                            <span x-text="user.username"></span>
                                            <span class="mx-0.5">•</span>
                                            <span class="truncate" x-text="user.email"></span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Hak Akses -->
                            <td class="px-5 py-3">
                                <div class="text-xs font-semibold text-slate-700 truncate" x-text="user.role ? user.role.name : '-'"></div>
                                <template x-if="user.role && user.role.name === 'Petugas Penghubung' && user.unit_pengolah">
                                    <div class="text-[10px] text-slate-500 mt-0.5 truncate flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[10px]">apartment</span>
                                        <span x-text="user.unit_pengolah.nama_bidang"></span>
                                    </div>
                                </template>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3 text-center">
                                <template x-if="user.is_active">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700">
                                        Aktif
                                    </span>
                                </template>
                                <template x-if="!user.is_active">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-700">
                                        Diblokir
                                    </span>
                                </template>
                            </td>

                            <!-- Keamanan -->
                            <td class="px-5 py-3 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <template x-if="user.google2fa_secret">
                                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded uppercase tracking-wider" title="2FA Aktif">2FA</span>
                                    </template>
                                    <template x-if="user.must_change_password">
                                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded uppercase tracking-wider" title="Wajib Setup Sandi">Setup</span>
                                    </template>
                                    <template x-if="!user.google2fa_secret && !user.must_change_password">
                                        <span class="text-slate-300">-</span>
                                    </template>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-3 text-right">
                                <div x-data="{ open: false }" class="relative inline-block text-left" @click.away="open = false">
                                    <button @click="open = !open" type="button" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors shadow-sm focus:outline-none">
                                        <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                    </button>
                                    
                                    <div x-show="open" x-transition.opacity.duration.200ms style="display: none;"
                                         class="origin-top-right absolute right-0 mt-2 w-44 rounded-xl shadow-lg shadow-slate-200/50 bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-slate-100 overflow-hidden">
                                        
                                        <div class="py-1">
                                            <button @click="openEditModal(user); open = false" 
                                                    class="group flex w-full items-center px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                                <span class="material-symbols-outlined text-[16px] mr-2 text-slate-400 group-hover:text-blue-600">edit</span> 
                                                Edit Pengguna
                                            </button>
                                        </div>
                                        
                                        <div class="py-1">
                                            <template x-if="user.id !== {{ auth()->id() }}">
                                                <button @click="confirmToggle(user); open = false" 
                                                        class="group flex w-full items-center px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 transition-colors">
                                                    <span class="material-symbols-outlined text-[16px] mr-2 text-slate-400" x-text="user.is_active ? 'block' : 'check_circle'"></span> 
                                                    <span x-text="user.is_active ? 'Blokir Akun' : 'Aktifkan Akun'"></span>
                                                </button>
                                            </template>
                                            <template x-if="user.google2fa_secret">
                                                <button @click="confirmReset2fa(user); open = false" 
                                                        class="group flex w-full items-center px-4 py-2 text-xs text-slate-700 hover:bg-amber-50 hover:text-amber-700 transition-colors">
                                                    <span class="material-symbols-outlined text-[16px] mr-2 text-slate-400 group-hover:text-amber-600">lock_reset</span> 
                                                    Reset 2FA
                                                </button>
                                            </template>
                                        </div>

                                        <template x-if="user.id !== {{ auth()->id() }}">
                                            <div class="py-1">
                                                <button @click="confirmDelete(user); open = false" 
                                                        class="group flex w-full items-center px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 transition-colors">
                                                    <span class="material-symbols-outlined text-[16px] mr-2 text-rose-500">delete</span> 
                                                    Hapus Permanen
                                                </button>
                                            </div>
                                        </template>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <!-- Empty State -->
                    <tr x-show="filteredItems.length === 0" style="display: none;">
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3 border border-slate-100">
                                    <span class="material-symbols-outlined text-[32px] text-slate-300">search_off</span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-700 mb-1">Pengguna tidak ditemukan</h3>
                                <p class="text-xs text-slate-500">Tidak ada pengguna yang cocok dengan filter atau pencarian Anda.</p>
                                <button type="button" @click="resetFilters()" x-show="search !== '' || filterRole !== 'all' || filterStatus !== 'all'"
                                        class="mt-4 text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                                    Reset Pencarian
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-between" x-show="totalPages > 1">
            <div class="text-xs text-slate-500">
                Menampilkan <span class="font-bold text-slate-700" x-text="getStartRecord()"></span> 
                hingga <span class="font-bold text-slate-700" x-text="getEndRecord()"></span> 
                dari <span class="font-bold text-slate-700" x-text="filteredItems.length"></span> pengguna
            </div>
            <div class="flex items-center gap-1">
                <button type="button" @click="prevPage" :disabled="currentPage === 1" 
                        class="w-8 h-8 flex items-center justify-center rounded bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                </button>
                <div class="flex gap-1 px-2">
                    <template x-for="page in totalPages" :key="page">
                        <button type="button" @click="goToPage(page)" 
                                class="w-8 h-8 flex items-center justify-center rounded text-xs font-bold transition-colors shadow-sm"
                                :class="currentPage === page ? 'bg-[#1a2b42] text-white border border-[#1a2b42]' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'"
                                x-text="page">
                        </button>
                    </template>
                </div>
                <button type="button" @click="nextPage" :disabled="currentPage === totalPages" 
                        class="w-8 h-8 flex items-center justify-center rounded bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </button>
            </div>
        </div>
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
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined" x-text="formMode === 'create' ? 'person_add' : 'edit'"></span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 leading-tight" x-text="formMode === 'create' ? 'Tambah Pengguna Baru' : 'Edit Pengguna'"></h3>
                                    <p class="text-xs text-slate-500 mt-0.5" x-text="formMode === 'create' ? 'Isi formulir untuk mendaftarkan pengguna baru.' : 'Ubah informasi akun dan hak akses.'"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nama -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Nama Lengkap <span class="text-rose-500">*</span></label>
                                    <input type="text" name="name" x-model="formData.name" required class="w-full text-xs border-slate-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 px-3 py-2 bg-slate-50 focus:bg-white transition-colors" placeholder="Cth: Budi Santoso">
                                </div>

                                <!-- Username -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Username <span class="text-rose-500">*</span></label>
                                    <input type="text" name="username" x-model="formData.username" required class="w-full text-xs border-slate-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 px-3 py-2 bg-slate-50 focus:bg-white transition-colors" placeholder="Cth: budi_s">
                                </div>

                                <!-- Email -->
                                <div class="flex flex-col gap-1.5 md:col-span-2">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Alamat Email <span class="text-rose-500">*</span></label>
                                    <input type="email" name="email" x-model="formData.email" required class="w-full text-xs border-slate-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 px-3 py-2 bg-slate-50 focus:bg-white transition-colors" placeholder="email@contoh.com">
                                </div>

                                <!-- Password -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                                        Kata Sandi <span x-show="formMode === 'create'" class="text-rose-500">*</span>
                                    </label>
                                    <input type="password" name="password" :required="formMode === 'create'" minlength="8" class="w-full text-xs border-slate-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 px-3 py-2 bg-slate-50 focus:bg-white transition-colors" :placeholder="formMode === 'create' ? 'Minimal 8 karakter' : '(Kosongkan jika tidak diubah)'">
                                </div>

                                <!-- Password Confirm -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                                        Konfirmasi Sandi <span x-show="formMode === 'create'" class="text-rose-500">*</span>
                                    </label>
                                    <input type="password" name="password_confirmation" :required="formMode === 'create'" minlength="8" class="w-full text-xs border-slate-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 px-3 py-2 bg-slate-50 focus:bg-white transition-colors" placeholder="Ulangi kata sandi">
                                </div>

                                <!-- Role Selection -->
                                <div class="md:col-span-2 border-t border-slate-100 pt-4 mt-2">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 block">Hak Akses (Role) <span class="text-rose-500">*</span></label>
                                    
                                    @if($hasSuperAdmin)
                                        <div x-show="formMode === 'create'" class="mb-3 p-2.5 bg-amber-50 text-amber-700 text-[10px] rounded border border-amber-200 font-medium">
                                            Role Super Admin sudah dipakai. Sistem hanya mengizinkan 1 Super Admin.
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-48 overflow-y-auto p-1">
                                        <template x-for="r in roles" :key="r.id">
                                            <label class="relative flex cursor-pointer rounded-lg border p-3 shadow-sm focus:outline-none transition-colors"
                                                   :class="[
                                                       formData.role_id == r.id ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50/30' : 'border-slate-200 hover:bg-slate-50',
                                                       (isSuperAdminDisabled(r.id) && formMode === 'create') || (isSuperAdminDisabled(r.id) && formMode === 'edit' && formData.role_id != r.id) ? 'opacity-50 cursor-not-allowed bg-slate-50' : ''
                                                   ]">
                                                <input type="radio" name="role_id" :value="r.id" x-model="formData.role_id" class="sr-only" required 
                                                       :disabled="(isSuperAdminDisabled(r.id) && formMode === 'create') || (isSuperAdminDisabled(r.id) && formMode === 'edit' && formData.role_id != r.id)">
                                                <span class="flex flex-1">
                                                    <span class="flex flex-col">
                                                        <span class="block text-xs font-bold text-slate-900" x-text="r.name"></span>
                                                        <span class="mt-0.5 flex items-center text-[10px] text-slate-500 line-clamp-2 leading-snug" x-text="r.description || 'Tidak ada deskripsi.'"></span>
                                                    </span>
                                                </span>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <!-- Unit Pengolah -->
                                <div class="flex flex-col gap-1.5 md:col-span-2 mt-2" x-show="isPetugasPenghubung" style="display: none;">
                                    <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Unit Pengolah / Bidang <span class="text-rose-500">*</span></label>
                                    <p class="text-[10px] text-slate-500 mb-1">Wajib dipilih karena pengguna diset sebagai Petugas Penghubung.</p>
                                    <select name="unit_pengolah_id" x-model="formData.unit_pengolah_id" :required="isPetugasPenghubung" class="w-full text-xs border-slate-300 rounded-lg focus:border-blue-500 px-3 py-2">
                                        <option value="">-- Pilih Unit Pengolah --</option>
                                        <template x-for="unit in unitPengolahs" :key="unit.id">
                                            <option :value="unit.id" x-text="unit.nama_bidang"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Status Aktif -->
                                <div class="md:col-span-2 mt-2 flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-lg">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" x-model="formData.is_active" :disabled="formMode === 'edit' && formData.id === {{ auth()->id() }}"
                                           class="rounded text-blue-600 focus:ring-blue-500 border-slate-300 h-4 w-4 transition-colors">
                                    <div>
                                        <label for="is_active" class="text-xs font-bold text-slate-700">Akun Aktif</label>
                                        <p class="text-[10px] text-slate-500">Hapus centang untuk memblokir login akun ini.</p>
                                        <template x-if="formMode === 'edit' && formData.id === {{ auth()->id() }}">
                                            <p class="text-[10px] text-rose-500 mt-0.5 font-medium">Anda tidak dapat menonaktifkan akun sendiri.</p>
                                        </template>
                                    </div>
                                    <!-- Hidden input for checkbox false state when disabled -->
                                    <template x-if="formMode === 'edit' && formData.id === {{ auth()->id() }}">
                                        <input type="hidden" name="is_active" value="1">
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 rounded-b-2xl border-t border-slate-100">
                            <button type="button" @click="closeFormModal()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                                <span x-text="formMode === 'create' ? 'Simpan Pengguna' : 'Simpan Perubahan'"></span>
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
                            <div class="w-14 h-14 rounded-full flex items-center justify-center mb-4"
                                 :class="confirmType === 'delete' ? 'bg-rose-100 text-rose-600' : (confirmType === 'reset' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600')">
                                <span class="material-symbols-outlined text-[28px]" 
                                      x-text="confirmType === 'delete' ? 'warning' : (confirmType === 'reset' ? 'lock_reset' : 'rule')"></span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2" x-text="confirmTitle"></h3>
                            <p class="text-xs text-slate-500 leading-relaxed" x-text="confirmMessage"></p>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-between gap-3 rounded-b-2xl border-t border-slate-100">
                        <button type="button" @click="confirmModalOpen = false" class="w-1/2 px-4 py-2.5 text-xs font-bold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <form :action="confirmUrl" method="POST" class="w-1/2 m-0">
                            @csrf
                            <template x-if="confirmType === 'delete'">
                                @method('DELETE')
                            </template>
                            <button type="submit" class="w-full px-4 py-2.5 text-xs font-bold text-white rounded-xl transition-colors shadow-sm"
                                    :class="confirmType === 'delete' ? 'bg-rose-600 hover:bg-rose-700' : (confirmType === 'reset' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-blue-600 hover:bg-blue-700')">
                                Ya, Lanjutkan
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
    Alpine.data('userManagement', (initialUsers) => ({
        users: initialUsers,
        roles: @json($roles),
        unitPengolahs: @json($unitPengolahs),
        hasSuperAdmin: @json($hasSuperAdmin),
        superAdminRole: @json($superAdminRole),
        
        search: '',
        filterRole: 'all',
        filterStatus: 'all',
        
        currentPage: 1,
        itemsPerPage: 10,
        
        // Modal states
        formModalOpen: false,
        formMode: 'create', // create, edit
        formAction: '',
        formData: {
            id: '', name: '', username: '', email: '', role_id: '', unit_pengolah_id: '', is_active: true
        },
        
        confirmModalOpen: false,
        confirmUrl: '',
        confirmTitle: '',
        confirmMessage: '',
        confirmType: '', // delete, toggle, reset
        
        get filteredItems() {
            let result = this.users;
            
            // Search filter
            if (this.search) {
                const q = this.search.toLowerCase();
                result = result.filter(u => 
                    u.name.toLowerCase().includes(q) || 
                    u.email.toLowerCase().includes(q) ||
                    (u.username && u.username.toLowerCase().includes(q))
                );
            }
            
            // Role filter
            if (this.filterRole !== 'all') {
                result = result.filter(u => u.role_id == this.filterRole);
            }
            
            // Status filter
            if (this.filterStatus === 'active') {
                result = result.filter(u => u.is_active);
            } else if (this.filterStatus === 'blocked') {
                result = result.filter(u => !u.is_active);
            } else if (this.filterStatus === 'password_change') {
                result = result.filter(u => u.must_change_password);
            }
            
            return result;
        },
        
        get paginatedItems() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredItems.slice(start, end);
        },
        
        get totalPages() {
            return Math.ceil(this.filteredItems.length / this.itemsPerPage);
        },
        
        applyFilters() {
            this.currentPage = 1;
        },
        
        resetFilters() {
            this.search = '';
            this.filterRole = 'all';
            this.filterStatus = 'all';
            this.currentPage = 1;
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },
        goToPage(page) {
            this.currentPage = page;
        },
        getStartRecord() {
            return this.filteredItems.length === 0 ? 0 : ((this.currentPage - 1) * this.itemsPerPage) + 1;
        },
        getEndRecord() {
            const end = this.currentPage * this.itemsPerPage;
            return end > this.filteredItems.length ? this.filteredItems.length : end;
        },
        
        // Modals
        openCreateModal() {
            this.formMode = 'create';
            this.formAction = "{{ route('admin.users.store') }}";
            this.formData = { id: '', name: '', username: '', email: '', role_id: '', unit_pengolah_id: '', is_active: true };
            this.formModalOpen = true;
        },
        openEditModal(user) {
            this.formMode = 'edit';
            this.formAction = `/admin/users/${user.id}`;
            this.formData = { 
                id: user.id, 
                name: user.name, 
                username: user.username,
                email: user.email, 
                role_id: user.role_id, 
                unit_pengolah_id: user.unit_pengolah_id || '', 
                is_active: user.is_active == 1 
            };
            this.formModalOpen = true;
        },
        closeFormModal() {
            this.formModalOpen = false;
        },
        
        confirmDelete(user) {
            this.confirmType = 'delete';
            this.confirmUrl = `/admin/users/${user.id}`;
            this.confirmTitle = 'Hapus Pengguna';
            this.confirmMessage = `Apakah Anda yakin ingin menghapus pengguna "${user.name}" secara permanen? Aksi ini tidak dapat dibatalkan.`;
            this.confirmModalOpen = true;
        },
        confirmToggle(user) {
            this.confirmType = 'toggle';
            this.confirmUrl = `/admin/users/${user.id}/toggle-active`;
            this.confirmTitle = user.is_active ? 'Blokir Pengguna' : 'Aktifkan Pengguna';
            this.confirmMessage = user.is_active 
                ? `Pengguna "${user.name}" tidak akan bisa login lagi ke dalam sistem. Yakin ingin memblokir?`
                : `Akses login untuk pengguna "${user.name}" akan dipulihkan. Yakin ingin mengaktifkan?`;
            this.confirmModalOpen = true;
        },
        confirmReset2fa(user) {
            this.confirmType = 'reset';
            this.confirmUrl = `/admin/users/${user.id}/reset-2fa`;
            this.confirmTitle = 'Reset 2FA';
            this.confirmMessage = `Pengguna "${user.name}" harus mengatur ulang Two-Factor Authentication saat login berikutnya. Lanjutkan?`;
            this.confirmModalOpen = true;
        },
        
        // Helpers
        get isPetugasPenghubung() {
            if (!this.formData.role_id) return false;
            const role = this.roles.find(r => r.id == this.formData.role_id);
            return role && role.name === 'Petugas Penghubung';
        },
        isSuperAdminDisabled(roleId) {
            if (!this.hasSuperAdmin || !this.superAdminRole) return false;
            return roleId === this.superAdminRole.id;
        }
    }));
});
</script>
@endsection
