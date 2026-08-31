<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PermohonanInformasi;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik
        $countBaru = PermohonanInformasi::where('status', 'masuk')->count();
        $countMenungguData = PermohonanInformasi::whereIn('status', ['menunggu_koordinasi', 'menunggu_data'])->count();
        $countSiapValidasi = PermohonanInformasi::where('status', 'siap_validasi')->count();
        $countTenggat = PermohonanInformasi::whereNotIn('status', ['selesai', 'ditolak'])
            ->where('created_at', '<=', now()->subDays(7))
            ->count();

        // Data Terbaru
        $permohonanTerbaru = PermohonanInformasi::orderBy('created_at', 'desc')->take(5)->get();

        // Data for Chart (Status Distribution)
        $chartStatus = [
            'Baru' => $countBaru,
            'Proses' => $countMenungguData,
            'Validasi' => $countSiapValidasi,
            'Selesai' => PermohonanInformasi::where('status', 'selesai')->count(),
            'Ditolak' => PermohonanInformasi::where('status', 'ditolak')->count(),
        ];

        // Data for Chart (Trend 6 Bulan Terakhir)
        $trendBulan = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = PermohonanInformasi::whereYear('created_at', $month->year)
                                        ->whereMonth('created_at', $month->month)
                                        ->count();
            $trendBulan[$month->translatedFormat('M Y')] = $count;
        }

        return view('admin.dashboard.pelaksana', compact(
            'countBaru', 
            'countMenungguData', 
            'countSiapValidasi', 
            'countTenggat',
            'permohonanTerbaru',
            'chartStatus',
            'trendBulan'
        ));
    }
}
