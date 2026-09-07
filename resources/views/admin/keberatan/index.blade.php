@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-800 m-0">Pengajuan Keberatan</h2>
            <p class="text-xs text-gray-500 m-0 mt-1">Kelola Sengketa & Keberatan Pemohon</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-md shadow-sm" role="alert">
            <span class="font-medium">Berhasil!</span> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md shadow-sm" role="alert">
            <span class="font-medium">Gagal!</span> {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50/50 p-4">
            <div class="flex gap-2">
                <a href="{{ route('admin.keberatan.index') }}" class="px-4 py-1.5 text-xs font-semibold rounded-full border {{ $currentStatus == '' ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">Semua</a>
                <a href="{{ route('admin.keberatan.index', ['status' => 'Masuk']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-full border {{ $currentStatus == 'Masuk' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">Masuk</a>
                <a href="{{ route('admin.keberatan.index', ['status' => 'Diproses']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-full border {{ $currentStatus == 'Diproses' ? 'bg-yellow-500 text-white border-yellow-500' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">Diproses</a>
                <a href="{{ route('admin.keberatan.index', ['status' => 'Selesai']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-full border {{ $currentStatus == 'Selesai' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">Selesai</a>
            </div>
        </div>
        
        <div class="w-full overflow-hidden">
            <table class="w-full table-fixed text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="w-[18%] px-4 py-3">No. Registrasi</th>
                        <th class="w-[18%] px-4 py-3">Pemohon</th>
                        <th class="w-[26%] px-4 py-3">Alasan Keberatan</th>
                        <th class="w-[14%] px-4 py-3">Tanggal Pengajuan</th>
                        <th class="w-[13%] px-4 py-3 text-center">Status</th>
                        <th class="w-[11%] px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($keberatan as $k)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-800 truncate">
                                {{ $k->permohonan_informasi->nomor_registrasi }}
                            </td>
                            <td class="px-4 py-3 truncate">
                                {{ $k->permohonan_informasi->nama_pemohon }}
                            </td>
                            <td class="px-4 py-3 truncate" title="{{ $k->alasan_keberatan }}">
                                {{ $k->alasan_keberatan }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $k->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border border-current/20 {{ $k->status->badgeClass() }} max-w-full" title="{{ $k->status->label() }}">
                                    <span class="truncate">{{ $k->status->label() }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <a href="{{ route('admin.keberatan.show', $k->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors shadow-2xs" title="Detail Keberatan">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">inbox</span>
                                    <p>Tidak ada data pengajuan keberatan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($keberatan->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $keberatan->links() }}
        </div>
        @endif
    </div>
</main>
@endsection
