@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-6 bg-[#f4f6f9] overflow-y-auto min-h-screen">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800 m-0">Ikhtisar Pelaksana</h2>
            <p class="text-[11px] font-medium text-gray-500 mt-0.5">Pantauan status permohonan informasi publik hari ini.</p>
        </div>
        <button class="bg-[#1a2b42] text-white px-3 py-1.5 rounded text-[11px] font-semibold hover:bg-[#121c2e] transition-colors shadow-sm flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[14px]">download</span>
            Unduh Laporan
        </button>
    </div>

    <!-- Summary Cards Bento -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1 -->
        <div class="bg-white rounded-md p-5 border border-gray-200 shadow-sm hover:shadow transition-shadow relative overflow-hidden group">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Permohonan Baru</p>
                    <h3 class="text-2xl font-bold text-gray-800 m-0">{{ $countBaru }}</h3>
                </div>
                <div class="w-10 h-10 rounded border border-gray-100 bg-gray-50 flex items-center justify-center text-gray-400">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;">inbox</span>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-green-600 font-medium">
                <span class="material-symbols-outlined text-[14px]">info</span>
                <span>Dari Desk Layanan</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-md p-5 border border-gray-200 shadow-sm hover:shadow transition-shadow relative overflow-hidden group">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Menunggu Data</p>
                    <h3 class="text-2xl font-bold text-gray-800 m-0">{{ $countMenungguData }}</h3>
                </div>
                <div class="w-10 h-10 rounded border border-gray-100 bg-gray-50 flex items-center justify-center text-gray-400">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;">hourglass_empty</span>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-blue-600 font-medium">
                <span class="material-symbols-outlined text-[14px]">sync</span>
                <span>Proses di Unit Pengolah</span>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-md p-5 border border-gray-200 shadow-sm hover:shadow transition-shadow relative overflow-hidden group">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Siap Validasi</p>
                    <h3 class="text-2xl font-bold text-gray-800 m-0">{{ $countSiapValidasi }}</h3>
                </div>
                <div class="w-10 h-10 rounded border border-gray-100 bg-gray-50 flex items-center justify-center text-gray-400">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;">fact_check</span>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-green-600 font-medium">
                <span class="material-symbols-outlined text-[14px]">task_alt</span>
                <span>Data telah terkumpul</span>
            </div>
        </div>

        <!-- Card 4 (Warning) -->
        <div class="bg-white rounded-md p-5 border border-red-200 shadow-sm hover:shadow transition-shadow relative overflow-hidden group">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-[11px] font-bold text-red-500 uppercase tracking-wider mb-1">Mendekati Tenggat</p>
                    <h3 class="text-2xl font-bold text-red-600 m-0">{{ $countTenggat }}</h3>
                </div>
                <div class="w-10 h-10 rounded border border-red-100 bg-red-50 flex items-center justify-center text-red-400">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;">warning</span>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-red-500 font-medium">
                <span class="material-symbols-outlined text-[14px]">error_outline</span>
                <span>> 7 Hari Kerja</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5">
        <!-- Status Chart -->
        <div class="bg-white rounded border border-gray-200 shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-800 m-0 mb-4">Distribusi Status</h3>
            <div class="relative h-48 w-full flex justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Trend Chart -->
        <div class="bg-white rounded border border-gray-200 shadow-sm p-5 lg:col-span-2">
            <h3 class="text-xs font-bold text-gray-800 m-0 mb-4">Tren Permohonan (6 Bulan Terakhir)</h3>
            <div class="relative h-48 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Data Table -->
    <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden mt-5">
        <div class="px-5 py-3 flex justify-between items-center border-b border-gray-200">
            <h3 class="text-xs font-bold text-gray-800 m-0 flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] text-[#f5d76e]">trending_up</span>
                Permohonan Terbaru
            </h3>
            <a href="{{ route('admin.permohonan.index') }}" class="text-blue-600 hover:text-blue-800 text-[11px] font-semibold transition-colors flex items-center gap-1">
                Lihat Semua <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-wider">
                        <th class="px-6 py-3 font-bold border-b border-gray-200">No. Registrasi</th>
                        <th class="px-6 py-3 font-bold border-b border-gray-200">Pemohon</th>
                        <th class="px-6 py-3 font-bold border-b border-gray-200">Informasi Diminta</th>
                        <th class="px-6 py-3 font-bold border-b border-gray-200">Status</th>
                        <th class="px-6 py-3 font-bold border-b border-gray-200 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($permohonanTerbaru as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 whitespace-nowrap text-gray-600 font-medium">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs border border-gray-200">{{ $p->nomor_registrasi }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="font-semibold text-gray-800">{{ $p->nama_pemohon }}</span>
                        </td>
                        <td class="px-6 py-3 text-gray-500 truncate max-w-[200px] text-xs">{{ Str::limit($p->rincian_informasi, 50) }}</td>
                        <td class="px-6 py-3">
                            @if($p->status == 'masuk')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Baru
                                </span>
                            @elseif(in_array($p->status, ['menunggu_koordinasi', 'menunggu_data']))
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-600 border border-purple-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Proses
                                </span>
                            @elseif($p->status == 'siap_validasi')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Diuji
                                </span>
                            @elseif($p->status == 'menunggu_ttd')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> TTE
                                </span>
                            @elseif($p->status == 'selesai')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-green-50 text-green-600 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-red-50 text-red-600 border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('admin.permohonan.show', $p->id) }}" class="inline-flex items-center justify-center bg-white border border-gray-300 text-blue-600 hover:bg-blue-50 transition-colors px-2 py-1 rounded shadow-sm text-xs font-semibold" title="Detail">
                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                            </a>
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
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($chartStatus)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($chartStatus)) !!},
                    backgroundColor: [
                        '#3b82f6', // Baru - Blue
                        '#a855f7', // Proses - Purple
                        '#f59e0b', // Validasi - Amber
                        '#10b981', // Selesai - Emerald
                        '#ef4444'  // Ditolak - Red
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10, family: "'Inter', sans-serif" }
                        }
                    }
                }
            }
        });

        // Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($trendBulan)) !!},
                datasets: [{
                    label: 'Jumlah Permohonan',
                    data: {!! json_encode(array_values($trendBulan)) !!},
                    backgroundColor: '#1a2b42',
                    borderRadius: 4,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 10 } },
                        grid: { color: '#f3f4f6' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endsection
