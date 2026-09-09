<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnitPengolah;
use App\Models\KategoriPemohon;
use App\Models\CaraMemperolehInformasi;
use App\Models\KategoriInformasiPublik;

class MasterDataController extends Controller
{
    private function checkAccess()
    {
        $user = auth()->user();
        if ($user->hasRole('Super Admin')) return;
        if (!$user->hasRole('Atasan PPID Pelaksana') && !$user->hasRole('PPID Pelaksana')) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola Master Data.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAccess();

        $initialTab = session('tab', $request->query('tab', 'bidang'));
        $validTabs = ['bidang', 'kategori-pemohon', 'cara-memperoleh', 'kategori-informasi'];
        if (!in_array($initialTab, $validTabs)) {
            $initialTab = 'bidang';
        }

        // Fetch all master data collections with relationship counts for instant client-side interactions
        $unitPengolahList = UnitPengolah::withCount(['users', 'permohonan_informasis'])->orderBy('nama_bidang', 'asc')->get();
        $kategoriPemohonList = KategoriPemohon::withCount('permohonan_informasis')->orderBy('nama_kategori', 'asc')->get();
        $caraMemperolehList = CaraMemperolehInformasi::withCount('permohonan_informasis')->orderBy('nama_cara', 'asc')->get();
        $kategoriInformasiList = KategoriInformasiPublik::withCount('informasi_publiks')->orderBy('nama_kategori', 'asc')->get();

        // Statistics counters
        $stats = [
            'bidang' => $unitPengolahList->count(),
            'kategori_pemohon' => $kategoriPemohonList->count(),
            'cara_memperoleh' => $caraMemperolehList->count(),
            'kategori_informasi' => $kategoriInformasiList->count(),
        ];

        return view('admin.master_data.index', compact(
            'initialTab',
            'stats',
            'unitPengolahList',
            'kategoriPemohonList',
            'caraMemperolehList',
            'kategoriInformasiList'
        ));
    }
}
