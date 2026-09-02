@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
        <div>
            <h2 class="text-lg font-bold text-gray-800 m-0">
                @if($currentStatus)
                    Permohonan: {{ ucwords(str_replace('_', ' ', $currentStatus)) }}
                @else
                    Daftar Semua Permohonan
                @endif
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">Kelola dan pantau seluruh permohonan informasi publik.</p>
        </div>
        
        <!-- Filter/Search -->
        <div class="flex items-center gap-2">
            <select class="bg-white border border-gray-200 text-gray-700 rounded shadow-sm text-xs py-1.5 px-3 focus:outline-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400 font-medium" onchange="window.location.href=this.value">
                <option value="{{ route('admin.permohonan.index') }}">Semua Status</option>
                <option value="{{ route('admin.permohonan.index', ['status' => 'diajukan']) }}" {{ $currentStatus == 'diajukan' ? 'selected' : '' }}>Permohonan Masuk</option>
                <option value="{{ route('admin.permohonan.index', ['status' => 'diverifikasi']) }}" {{ $currentStatus == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                <option value="{{ route('admin.permohonan.index', ['status' => 'ditugaskan']) }}" {{ $currentStatus == 'ditugaskan' ? 'selected' : '' }}>Ditugaskan</option>
                <option value="{{ route('admin.permohonan.index', ['status' => 'menunggu_data']) }}" {{ $currentStatus == 'menunggu_data' ? 'selected' : '' }}>Menunggu Data</option>
                <option value="{{ route('admin.permohonan.index', ['status' => 'data_diuji']) }}" {{ $currentStatus == 'data_diuji' ? 'selected' : '' }}>Data Diuji</option>
                <option value="{{ route('admin.permohonan.index', ['status' => 'menunggu_tanda_tangan']) }}" {{ $currentStatus == 'menunggu_tanda_tangan' ? 'selected' : '' }}>Menunggu Tanda Tangan</option>
                <option value="{{ route('admin.permohonan.index', ['status' => 'selesai']) }}" {{ $currentStatus == 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-visible mt-2">
        <div class="w-full">
            <table class="w-full text-left border-collapse text-sm text-gray-600">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px]">
                        <th class="px-6 py-3 font-bold border-b border-gray-200">No. Registrasi</th>
                        <th class="px-6 py-3 font-bold border-b border-gray-200">Pemohon</th>
                        <th class="px-6 py-3 font-bold border-b border-gray-200">Tanggal Masuk</th>
                        <th class="px-6 py-3 font-bold border-b border-gray-200">Status</th>
                        <th class="px-6 py-3 font-bold border-b border-gray-200 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($permohonan as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 whitespace-nowrap text-gray-600 font-medium">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs border border-gray-200">{{ $p->nomor_registrasi }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="font-semibold text-gray-800">{{ $p->nama_pemohon }}</span>
                        </td>
                        <td class="px-6 py-3 text-gray-500">
                            {{ $p->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[11px] font-semibold border {{ $p->status->badgeClass() }} border-opacity-30">
                                {{ $p->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors px-2 py-1 rounded shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]">more_vert</span>
                                </button>
                                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-36 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100" style="display: none;">
                                    <div class="py-1">
                                        <a href="{{ route('admin.permohonan.show', $p->id) }}" class="group flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                                            <span class="material-symbols-outlined text-[16px] mr-2">visibility</span> Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">Belum ada data permohonan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Placeholder -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $permohonan->links('pagination::tailwind') }}
        </div>
    </div>
</main>
@endsection
