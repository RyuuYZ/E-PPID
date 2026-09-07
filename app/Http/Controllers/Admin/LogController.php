<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PermohonanLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'permohonan'); // 'permohonan' atau 'sistem'
        $search = $request->get('search');

        if ($tab === 'sistem') {
            $query = ActivityLog::with('user')->orderBy('created_at', 'desc');
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%")
                      ->orWhereHas('user', function($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }
            $logs = $query->paginate(15)->withQueryString();
        } else {
            $query = PermohonanLog::with(['user', 'permohonan_informasi'])->orderBy('created_at', 'desc');
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('aksi', 'like', "%{$search}%")
                      ->orWhere('catatan', 'like', "%{$search}%")
                      ->orWhere('tahapan_proses', 'like', "%{$search}%")
                      ->orWhereHas('permohonan_informasi', function($p) use ($search) {
                          $p->where('nomor_registrasi', 'like', "%{$search}%")
                            ->orWhere('nama_pemohon', 'like', "%{$search}%");
                      })
                      ->orWhereHas('user', function($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%");
                      });
                });
            }
            $logs = $query->paginate(15)->withQueryString();
        }

        $countPermohonan = PermohonanLog::count();
        $countSistem = ActivityLog::count();

        return view('admin.logs.index', compact('logs', 'tab', 'countPermohonan', 'countSistem'));
    }
}
