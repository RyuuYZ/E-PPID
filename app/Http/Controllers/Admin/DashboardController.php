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

        return view('admin.dashboard.pelaksana', compact(
            'countBaru', 
            'countMenungguData', 
            'countSiapValidasi', 
            'countTenggat',
            'permohonanTerbaru'
        ));
    }
}
