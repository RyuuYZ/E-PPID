@extends('admin.layouts.settings')

@section('settings_content')
<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
        <div>
            <h2 class="text-lg font-bold text-gray-800 m-0">Log Keamanan & Aktivitas</h2>
            <p class="text-xs text-gray-500 mt-0.5">Pantau seluruh riwayat aksi pengguna, perubahan sistem, dan peringatan keamanan.</p>
        </div>
        
        <!-- Search -->
        <form action="{{ route('admin.logs.index') }}" method="GET" class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau aksi..." class="pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded focus:border-[#1a2b42] focus:ring-1 focus:ring-[#1a2b42] w-64 shadow-sm">
            <span class="material-symbols-outlined absolute left-2 top-2 text-[16px] text-gray-400">search</span>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden mt-2">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px]">
                    <th class="px-6 py-3 font-bold border-b border-gray-200 w-48">Waktu (WIB)</th>
                    <th class="px-6 py-3 font-bold border-b border-gray-200">Pengguna</th>
                    <th class="px-6 py-3 font-bold border-b border-gray-200">Aksi</th>
                    <th class="px-6 py-3 font-bold border-b border-gray-200">Deskripsi Detail</th>
                    <th class="px-6 py-3 font-bold border-b border-gray-200 w-32">Alamat IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 text-gray-500 text-[11px] whitespace-nowrap">
                        {{ $log->created_at->format('d M Y - H:i:s') }}
                    </td>
                    <td class="px-6 py-3">
                        @if($log->user)
                            <div class="font-bold text-gray-800 text-xs">{{ $log->user->name }}</div>
                            <div class="text-[10px] text-gray-500">{{ $log->user->email }}</div>
                        @else
                            <span class="text-gray-400 italic text-xs">Sistem / Guest</span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wider border border-gray-200 bg-gray-50 text-gray-600">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-gray-700 text-xs">
                        {{ $log->description ?? '-' }}
                    </td>
                    <td class="px-6 py-3 text-gray-500 text-[10px] font-mono">
                        {{ $log->ip_address ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">Tidak ada catatan aktivitas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
