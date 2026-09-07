@extends('admin.layouts.app')

@section('content')
<main class="flex-1 p-5 md:p-8 bg-[#f8fafc] overflow-y-auto min-h-screen">
    <!-- Header Dashboard -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">space_dashboard</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 m-0 tracking-tight">Dashboard Eksekutif E-PPID</h1>
            </div>
            <p class="text-xs text-slate-500 ml-10">Pantauan terpadu status pelayanan informasi publik Bappeda Kabupaten Ciamis</p>
        </div>
        <div class="flex items-center gap-2.5 self-stretch sm:self-auto justify-end">
            <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200/80 text-xs font-semibold text-slate-600 shadow-2xs">
                <span class="material-symbols-outlined text-[15px] text-slate-400">calendar_month</span>
                <span>{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
            </div>
            <a href="{{ route('admin.permohonan.create') }}" class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition-colors shadow-xs">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>Permohonan Baru</span>
            </a>
        </div>
    </div>

    <!-- Bento Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 mb-6">
        <!-- Card 1: Total Permohonan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Permohonan</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 m-0 tracking-tight">{{ $totalPermohonan }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">folder_shared</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Tingkat Penyelesaian:</span>
                <span class="font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">{{ $rateSelesai }}%</span>
            </div>
        </div>

        <!-- Card 2: Permohonan Aktif (Dalam Proses) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Sedang Diproses</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 m-0 tracking-tight">{{ $countProses }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">pending_actions</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1 font-medium text-slate-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> {{ $countBaru }} Verifikasi
                </span>
                <span>•</span>
                <span class="inline-flex items-center gap-1 font-medium text-slate-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> {{ $countKoordinasi }} Koordinasi
                </span>
            </div>
        </div>

        <!-- Card 3: Uji & Pengesahan TTE -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Uji & Siap TTE</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 m-0 tracking-tight">{{ $countValidasi + $countMenungguTTE }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">draw</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1 font-medium text-slate-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span> {{ $countValidasi }} Diuji
                </span>
                <span>•</span>
                <span class="inline-flex items-center gap-1 font-medium text-slate-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> {{ $countMenungguTTE }} Menunggu TTE
                </span>
            </div>
        </div>

        <!-- Card 4: Selesai -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Selesai / Terlayani</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-emerald-600 m-0 tracking-tight">{{ $countSelesai }}</h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">task_alt</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Jawaban Resmi:</span>
                <span class="font-bold text-slate-700">Telah Diserahkan</span>
            </div>
        </div>
    </div>

    <!-- Charts Section (Distribusi Status & Tren Bulanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Status Distribution Chart -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 md:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-blue-600">pie_chart</span>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0">Distribusi Status Alur</h3>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500 bg-slate-50 px-2.5 py-0.5 rounded-full border border-slate-200/60">{{ $totalPermohonan }} Total</span>
                </div>
                
                <div class="relative h-52 w-full flex items-center justify-center my-2">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Custom Legend Breakdown -->
            <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px]">
                @php
                    $colors = [
                        'Baru & Verifikasi' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-700'],
                        'Koordinasi Data' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-700'],
                        'Uji & Validasi' => ['bg' => 'bg-cyan-500', 'text' => 'text-cyan-700'],
                        'Pengesahan TTE' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-700'],
                        'Selesai' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-700'],
                    ];
                @endphp
                @foreach($chartStatus as $label => $val)
                <div class="flex items-center justify-between p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                    <span class="flex items-center gap-1.5 text-slate-600 font-medium truncate">
                        <span class="w-2 h-2 rounded-full {{ $colors[$label]['bg'] ?? 'bg-slate-400' }}"></span>
                        <span class="truncate">{{ $label }}</span>
                    </span>
                    <strong class="text-slate-800 ml-1 font-bold">{{ $val }}</strong>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Trend Chart (6 Bulan Terakhir) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 md:p-6 lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-blue-600">bar_chart</span>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0">Tren Permohonan Masuk (6 Bulan Terakhir)</h3>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium">Bappeda Ciamis</span>
                </div>
                
                <div class="relative h-64 w-full">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-xs bg-blue-600"></span>
                    <span>Volume Permohonan per Bulan</span>
                </span>
                <span class="font-semibold text-slate-700">Total Akumulatif: {{ array_sum($trendBulan) }} Permohonan</span>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Permohonan Terbaru (Full Width) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 bg-slate-50/70">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                    <span class="material-symbols-outlined text-[18px]">history_edu</span>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0">Permohonan Informasi Terbaru</h3>
                    <p class="text-[11px] text-slate-500 m-0">Daftar transaksi permohonan informasi publik terkini</p>
                </div>
            </div>
            <a href="{{ route('admin.permohonan.index') }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold transition-colors inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-blue-50">
                <span>Lihat Semua Permohonan</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>
        
        <div class="w-full overflow-hidden">
            <table class="w-full table-fixed text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-[10px] uppercase tracking-wider border-b border-slate-100">
                        <th class="w-[18%] px-3.5 py-3.5 font-bold">No. Registrasi</th>
                        <th class="w-[17%] px-3.5 py-3.5 font-bold">Pemohon & Kategori</th>
                        <th class="w-[21%] px-3.5 py-3.5 font-bold">Rincian Informasi</th>
                        <th class="w-[11%] px-3.5 py-3.5 font-bold">Tanggal Masuk</th>
                        <th class="w-[21%] px-3.5 py-3.5 font-bold">Status Alur</th>
                        <th class="w-[12%] px-3.5 py-3.5 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($permohonanTerbaru as $p)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-3.5 py-3 font-mono font-bold text-slate-800 truncate">
                            <a href="{{ route('admin.permohonan.show', $p->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                {{ $p->nomor_registrasi }}
                            </a>
                        </td>
                        <td class="px-3.5 py-3">
                            <span class="font-bold text-slate-800 block text-xs truncate" title="{{ $p->nama_pemohon }}">{{ $p->nama_pemohon }}</span>
                            <span class="text-[10px] text-slate-400 font-medium block truncate">{{ $p->kategori_pemohon->nama_kategori ?? 'Perorangan' }}</span>
                        </td>
                        <td class="px-3.5 py-3 text-slate-600 truncate leading-relaxed" title="{{ $p->rincian_informasi }}">
                            {{ Str::limit($p->rincian_informasi, 45) }}
                        </td>
                        <td class="px-3.5 py-3 whitespace-nowrap text-slate-500 text-[11px]">
                            <span class="font-medium text-slate-700 block">{{ $p->created_at->format('d M Y') }}</span>
                            <span class="text-[10px] text-slate-400">{{ $p->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-3.5 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9.5px] font-bold uppercase tracking-wide border border-current/20 {{ $p->status->badgeClass() }} shadow-2xs max-w-full" title="{{ $p->status->label() }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70 shrink-0"></span>
                                <span class="truncate">{{ $p->status->label() }}</span>
                            </span>
                        </td>
                        <td class="px-3.5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.permohonan.show', $p->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white text-slate-700 hover:text-blue-600 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 transition-colors shadow-2xs text-[11px] font-semibold" title="Lihat Detail">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                <span>Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400">Belum ada data permohonan tercatat.</td>
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
        // Status Distribution Donut Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($chartStatus)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($chartStatus)) !!},
                    backgroundColor: [
                        '#3b82f6', // Baru & Verifikasi - Blue
                        '#f59e0b', // Koordinasi Data - Amber
                        '#06b6d4', // Uji & Validasi - Cyan
                        '#a855f7', // Pengesahan TTE - Purple
                        '#10b981'  // Selesai - Emerald
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false // We use our custom clean legend pills below
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: "'Inter', sans-serif", size: 12, weight: 'bold' },
                        bodyFont: { family: "'Inter', sans-serif", size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                        boxPadding: 4
                    }
                }
            }
        });

        // Trend 6 Bulan Bar Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($trendBulan)) !!},
                datasets: [{
                    label: 'Jumlah Permohonan',
                    data: {!! json_encode(array_values($trendBulan)) !!},
                    backgroundColor: '#2563eb',
                    hoverBackgroundColor: '#1d4ed8',
                    borderRadius: 8,
                    barPercentage: 0.45,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1, 
                            font: { family: "'Inter', sans-serif", size: 11 },
                            color: '#64748b'
                        },
                        grid: { 
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { 
                            font: { family: "'Inter', sans-serif", size: 11, weight: 'bold' },
                            color: '#475569'
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: "'Inter', sans-serif", size: 12, weight: 'bold' },
                        bodyFont: { family: "'Inter', sans-serif", size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' Permohonan Masuk';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
