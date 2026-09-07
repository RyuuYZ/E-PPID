<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanInformasi;
use App\Models\PengajuanKeberatan;
use App\Models\UnitPengolah;
use App\Models\PermohonanLog;

class DashboardController extends Controller
{
    public function index()
    {
        // Total keseluruhan
        $totalPermohonan = PermohonanInformasi::count();

        // Metrik Alur Penanganan
        $countBaru = PermohonanInformasi::whereIn('status', ['diajukan', 'diverifikasi', 'menunggu_kelengkapan'])->count();
        $countKoordinasi = PermohonanInformasi::whereIn('status', ['ditugaskan', 'menunggu_data'])->count();
        $countValidasi = PermohonanInformasi::where('status', 'data_diuji')->count();
        $countMenungguTTE = PermohonanInformasi::whereIn('status', ['menunggu_tanda_tangan', 'ditandatangani'])->count();
        $countSelesai = PermohonanInformasi::where('status', 'selesai')->count();
        $countProses = $countBaru + $countKoordinasi + $countValidasi + $countMenungguTTE;

        // Persentase Tingkat Penyelesaian
        $rateSelesai = $totalPermohonan > 0 ? round(($countSelesai / $totalPermohonan) * 100, 1) : 0;

        // SLA Tenggat (> 7 hari kerja atau batas terlampaui)
        $countTenggat = PermohonanInformasi::whereNotIn('status', ['selesai', 'ditolak', 'ditutup_tidak_lengkap'])
            ->where('created_at', '<=', now()->subDays(7))
            ->count();

        // Data Permohonan Terbaru (dengan relasi kategori)
        $permohonanTerbaru = PermohonanInformasi::with('kategori_pemohon')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Data untuk Grafik Distribusi Status Alur
        $chartStatus = [
            'Baru & Verifikasi' => $countBaru,
            'Koordinasi Data' => $countKoordinasi,
            'Uji & Validasi' => $countValidasi,
            'Pengesahan TTE' => $countMenungguTTE,
            'Selesai' => $countSelesai,
        ];

        // Data untuk Grafik Tren 6 Bulan Terakhir
        $trendBulan = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = PermohonanInformasi::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $trendBulan[$month->translatedFormat('M Y')] = $count;
        }

        // Data Master Unit & Aktivitas Terbaru
        $totalUnit = UnitPengolah::count();
        $totalKeberatan = PengajuanKeberatan::count();
        $recentLogs = PermohonanLog::with(['user', 'permohonan_informasi'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard.pelaksana', compact(
            'totalPermohonan',
            'countBaru',
            'countKoordinasi',
            'countValidasi',
            'countMenungguTTE',
            'countSelesai',
            'countProses',
            'countTenggat',
            'rateSelesai',
            'totalUnit',
            'totalKeberatan',
            'permohonanTerbaru',
            'chartStatus',
            'trendBulan',
            'recentLogs'
        ));
    }
}
