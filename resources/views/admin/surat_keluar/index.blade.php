@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
        <div>
            <h2 class="text-lg font-bold text-gray-800 m-0">Surat Keluar</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kelola draft dan penerbitan Surat Keluar Bappeda.</p>
        </div>
        <a href="{{ route('admin.surat-keluar.create') }}" class="inline-flex items-center gap-1.5 bg-[#1a2b42] text-white font-semibold rounded px-3 py-1.5 hover:bg-[#121c2e] transition-colors shadow-sm text-xs">
            <span class="material-symbols-outlined text-[16px]">add</span>
            Buat Draft Surat
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-visible mt-2">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px]">
                    <th class="px-6 py-3 font-bold border-b border-gray-200">Tujuan / Perihal</th>
                    <th class="px-6 py-3 font-bold border-b border-gray-200">Nomor Surat</th>
                    <th class="px-6 py-3 font-bold border-b border-gray-200">Tanggal Keluar</th>
                    <th class="px-6 py-3 font-bold border-b border-gray-200">Status</th>
                    <th class="px-6 py-3 font-bold border-b border-gray-200 text-right w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($suratKeluars as $sk)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3">
                        <div class="font-bold text-gray-800 text-[13px]">{{ $sk->tujuan }}</div>
                        <div class="text-[11px] text-gray-500 mt-0.5 truncate max-w-[200px]">{{ $sk->perihal }}</div>
                    </td>
                    <td class="px-6 py-3 text-gray-700 text-xs">{{ $sk->nomor_surat ?? '(Belum Ditetapkan)' }}</td>
                    <td class="px-6 py-3 text-gray-500 text-xs">
                        {{ $sk->tanggal_keluar ? \Carbon\Carbon::parse($sk->tanggal_keluar)->translatedFormat('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-3">
                        @if($sk->status == 'Draft')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600 border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Draft
                            </span>
                        @elseif($sk->status == 'Menunggu TTE')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-yellow-50 text-yellow-600 border border-yellow-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> TTE
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-green-50 text-green-600 border border-green-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Terkirim
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-right">
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors px-2 py-1 rounded shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">more_vert</span>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100" style="display: none;">
                                <div class="py-1">
                                    @if($sk->file_draft)
                                        <a href="{{ Storage::url($sk->file_draft) }}" target="_blank" class="group flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                                            <span class="material-symbols-outlined text-[16px] mr-2">visibility</span> Lihat Draft
                                        </a>
                                    @endif
                                    
                                    @if($sk->status == 'Menunggu TTE' || $sk->status == 'Draft')
                                        @if(auth()->user()->hasRole('Atasan PPID Pelaksana'))
                                        <form action="{{ route('admin.surat-keluar.approve-tte', $sk->id) }}" method="POST" class="m-0" onsubmit="return confirm('Anda yakin akan menandatangani secara elektronik surat ini?');">
                                            @csrf
                                            <button type="submit" class="group flex w-full items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-green-600">
                                                <span class="material-symbols-outlined text-[16px] mr-2">draw</span> Tanda Tangani (TTE)
                                            </button>
                                        </form>
                                        @endif
                                    @endif
                                    
                                    @if($sk->status == 'Terkirim')
                                        <button type="button" class="group flex w-full items-center px-4 py-2 text-xs text-gray-500 hover:bg-gray-50 hover:text-gray-700 cursor-help" title="Ditandatangani oleh: {{ $sk->penandatangan->name ?? 'Sistem' }}&#10;UUID: {{ $sk->kode_verifikasi }}">
                                            <span class="material-symbols-outlined text-[16px] mr-2">verified</span> TTE Terverifikasi
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">Belum ada data surat keluar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $suratKeluars->links() }}
    </div>
</main>
@endsection
