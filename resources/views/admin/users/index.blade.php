@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
        <div>
            <h2 class="text-lg font-bold text-gray-800 m-0">Manajemen Pengguna</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kelola akun, hak akses, dan status pemblokiran pengguna.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-1.5 bg-[#1a2b42] text-white font-semibold rounded px-3 py-1.5 hover:bg-[#121c2e] transition-colors shadow-sm text-xs">
            <span class="material-symbols-outlined text-[16px]">person_add</span>
            Tambah Pengguna
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden mt-2">
        <table class="w-full table-fixed text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px]">
                    <th class="w-[45%] px-4 py-3 font-bold border-b border-gray-200">Nama Lengkap & Email</th>
                    <th class="w-[35%] px-4 py-3 font-bold border-b border-gray-200">Hak Akses (Role)</th>
                    <th class="w-[10%] px-4 py-3 font-bold border-b border-gray-200 text-center">Status</th>
                    <th class="w-[10%] px-4 py-3 font-bold border-b border-gray-200 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors {{ !$user->is_active ? 'bg-red-50/30' : '' }}">
                    <td class="px-4 py-3">
                        <div class="font-bold text-gray-800 truncate">{{ $user->name }}</div>
                        <div class="text-gray-500 text-xs mt-0.5 truncate">{{ $user->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-700 text-xs font-semibold">
                        <div class="truncate">{{ $user->role->name ?? '-' }}</div>
                        @if($user->hasRole('Petugas Penghubung') && $user->unit_pengolah)
                            <div class="text-[10px] text-gray-500 font-normal mt-0.5 truncate">
                                Unit: {{ $user->unit_pengolah->nama_bidang }}
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        @if($user->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700">Diblokir</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors px-2 py-1 rounded shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">more_vert</span>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100" style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="group flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                                        <span class="material-symbols-outlined text-[16px] mr-2">edit</span> Edit Pengguna
                                    </a>
                                    
                                    @if($user->google2fa_secret)
                                    <form action="{{ route('admin.users.reset-2fa', $user->id) }}" method="POST" class="m-0" onsubmit="return confirm('Reset autentikasi 2FA untuk pengguna ini?');">
                                        @csrf
                                        <button type="submit" class="group flex w-full items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-orange-600">
                                            <span class="material-symbols-outlined text-[16px] mr-2">lock_reset</span> Reset 2FA
                                        </button>
                                    </form>
                                    @endif

                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="m-0" onsubmit="return confirm('Yakin ingin menghapus pengguna ini secara permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="group flex w-full items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-red-600">
                                            <span class="material-symbols-outlined text-[16px] mr-2">delete</span> Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">Belum ada pengguna.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</main>
@endsection
