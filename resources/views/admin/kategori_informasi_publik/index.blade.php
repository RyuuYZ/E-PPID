@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
        <div>
            <h2 class="text-lg font-bold text-gray-800 m-0">Kategori Informasi Publik</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kelola data kategori ketersediaan informasi publik.</p>
        </div>
        <a href="{{ route('admin.kategori-informasi-publik.create') }}" class="inline-flex items-center gap-1.5 bg-[#1a2b42] text-white font-semibold rounded px-3 py-1.5 hover:bg-[#121c2e] transition-colors shadow-sm text-xs">
            <span class="material-symbols-outlined text-[16px]">add</span>
            Tambah Kategori
        </a>
    </div>

    @if(session('success'))
        <div class="p-3 mb-5 text-[13px] text-green-700 bg-green-50 border border-green-200 rounded shadow-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 mb-5 text-[13px] text-red-700 bg-red-50 border border-red-200 rounded shadow-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden mt-2">
        <table class="w-full table-fixed text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px]">
                    <th class="px-4 py-3 font-bold border-b border-gray-200 w-[8%]">ID</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200 w-[32%]">Kategori</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200 w-[22%]">Tampilan (Ikon/Warna)</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200 w-[26%]">Deskripsi</th>
                    <th class="px-4 py-3 font-bold border-b border-gray-200 text-right w-[12%]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($kategori as $k)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-600 font-medium">
                        <span class="bg-gray-100 px-2 py-1 rounded text-xs border border-gray-200">{{ $k->id }}</span>
                    </td>
                    <td class="px-4 py-3 font-semibold text-gray-800 truncate">{{ $k->nama_kategori }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 flex items-center justify-center rounded {{ $k->bg_color }} {{ $k->text_color }}">
                                <span class="material-symbols-outlined text-[14px]">{{ $k->icon }}</span>
                            </div>
                            <span class="text-[11px] text-gray-400 font-mono">{{ $k->icon }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $k->deskripsi ?? '-' }}</td>
                    <td class="px-6 py-3 text-right">
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors px-2 py-1 rounded shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">more_vert</span>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-36 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100" style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('admin.kategori-informasi-publik.edit', $k->id) }}" class="group flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                                        <span class="material-symbols-outlined text-[16px] mr-2">edit</span> Edit
                                    </a>
                                    <form action="{{ route('admin.kategori-informasi-publik.destroy', $k->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus kategori ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="group flex w-full items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-red-600">
                                            <span class="material-symbols-outlined text-[16px] mr-2">delete</span> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">Belum ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $kategori->links('vendor.pagination.custom', ['resourceName' => 'KATEGORI INFORMASI']) }}
    </div>
</main>
@endsection
