@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">history</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 m-0 tracking-tight">Log Aktivitas & Audit Trail</h1>
            </div>
            <p class="text-xs text-slate-500 ml-10">Rekam jejak seluruh penanganan permohonan dan riwayat operasional sistem</p>
        </div>

        <!-- Search Box -->
        <form action="{{ route('admin.logs.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="relative flex-1 sm:w-72">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-[16px] text-slate-400">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi, catatan, atau nama..." class="w-full pl-9 pr-4 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 shadow-2xs font-medium text-slate-800 transition-all">
            </div>
            @if(request('search'))
            <a href="{{ route('admin.logs.index', ['tab' => $tab]) }}" class="px-2.5 py-2 text-xs text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors font-medium">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabs Navigasi -->
    <div class="flex items-center gap-2 mb-5 border-b border-slate-200/80 pb-3">
        <a href="{{ route('admin.logs.index', ['tab' => 'permohonan']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $tab === 'permohonan' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
            <span class="material-symbols-outlined text-[16px]">assignment</span>
            <span>Alur Permohonan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'permohonan' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $countPermohonan }}
            </span>
        </a>

        <a href="{{ route('admin.logs.index', ['tab' => 'sistem']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $tab === 'sistem' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
            <span class="material-symbols-outlined text-[16px]">security</span>
            <span>Sistem & Keamanan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'sistem' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $countSistem }}
            </span>
        </a>
    </div>

    <!-- Tabel Data Log -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            @if($tab === 'permohonan')
            <!-- TAB: LOG ALUR PERMOHONAN -->
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 text-slate-500 text-[10px] uppercase tracking-wider border-b border-slate-100">
                        <th class="px-6 py-3.5 font-bold w-44">Waktu</th>
                        <th class="px-6 py-3.5 font-bold">Aktor / Pengguna</th>
                        <th class="px-6 py-3.5 font-bold">No. Registrasi</th>
                        <th class="px-6 py-3.5 font-bold">Tahapan / Aksi</th>
                        <th class="px-6 py-3.5 font-bold">Catatan Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800 block text-[11px]">{{ $log->created_at->format('d M Y, H:i') }} WIB</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                                    {{ substr($log->user->name ?? 'S', 0, 1) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 block text-xs">{{ $log->user->name ?? 'Sistem Otomatis' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $log->user->role->name ?? 'System' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->permohonan_informasi)
                            <a href="{{ route('admin.permohonan.show', $log->permohonan_informasi->id) }}" class="inline-flex items-center gap-1 font-mono font-bold text-blue-600 hover:underline">
                                <span>{{ $log->permohonan_informasi->nomor_registrasi }}</span>
                                <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                            </a>
                            <span class="block text-[10px] text-slate-400 truncate max-w-[160px]">{{ $log->permohonan_informasi->nama_pemohon }}</span>
                            @else
                            <span class="text-slate-400 italic text-[11px]">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs">
                                <span class="material-symbols-outlined text-[13px]">bolt</span>
                                {{ $log->aksi }}
                            </span>
                            @if($log->tahapan_proses)
                            <span class="block text-[10px] text-slate-400 mt-1 uppercase font-semibold tracking-wider">Tahap: {{ $log->tahapan_proses }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-700 text-xs">
                            @if($log->catatan)
                            <div class="p-2.5 bg-slate-50 border border-slate-200/60 rounded-xl text-[11px] leading-relaxed italic text-slate-600">
                                "{{ $log->catatan }}"
                            </div>
                            @else
                            <span class="text-slate-400 italic text-[11px]">Tidak ada catatan khusus.</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">Belum ada riwayat alur permohonan yang sesuai filter pencarian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @else
            <!-- TAB: LOG SISTEM & KEAMANAN -->
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 text-slate-500 text-[10px] uppercase tracking-wider border-b border-slate-100">
                        <th class="px-6 py-3.5 font-bold w-44">Waktu (WIB)</th>
                        <th class="px-6 py-3.5 font-bold">Pengguna</th>
                        <th class="px-6 py-3.5 font-bold">Aksi Operasi</th>
                        <th class="px-6 py-3.5 font-bold">Deskripsi Perubahan</th>
                        <th class="px-6 py-3.5 font-bold w-36">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800 block text-[11px]">{{ $log->created_at->format('d M Y, H:i:s') }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->user)
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">
                                    {{ substr($log->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 block text-xs">{{ $log->user->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $log->user->email }}</span>
                                </div>
                            </div>
                            @else
                            <span class="text-slate-400 italic text-xs">Sistem / Tamu</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-700 text-xs leading-relaxed">
                            {{ $log->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">Tidak ada log keamanan/sistem yang tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @endif
        </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</main>
@endsection
