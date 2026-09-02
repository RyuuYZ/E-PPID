<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $kategoriInformasi = \App\Models\KategoriInformasiPublik::all();
    return view('welcome', compact('kategoriInformasi'));
})->name('home');

Route::get('/permohonan/baru', function () {
    $kategoriPemohons = \App\Models\KategoriPemohon::all();
    $caraMemperoleh = \App\Models\CaraMemperolehInformasi::all();
    return view('permohonan', compact('kategoriPemohons', 'caraMemperoleh'));
})->name('permohonan.create');

Route::post('/permohonan/simpan', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'nama_pemohon' => 'required|string|max:255',
        'kategori_pemohon_id' => 'required|exists:kategori_pemohons,id',
        'nik_atau_no_badan_hukum' => 'required|string|max:50',
        'no_telp' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'alamat' => 'required|string',
        'subjek' => 'required|string',
        'rincian_informasi' => 'required|string',
        'tujuan_penggunaan' => 'required|string',
        'cara_memperoleh_informasi_id' => 'required|exists:cara_memperoleh_informasis,id',
        'file_identitas' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ]);

    if ($request->hasFile('file_identitas')) {
        $path = $request->file('file_identitas')->store('identitas', 'public');
        $validated['file_identitas'] = $path;
    }

    $validated['nomor_registrasi'] = 'REG-' . date('YmdHis') . '-' . rand(1000, 9999);
    $validated['rincian_informasi'] = $validated['subjek'] . "\n\n" . $validated['rincian_informasi'];
    $validated['status'] = \App\Enums\PermohonanStatus::Diajukan->value;

    \App\Models\PermohonanInformasi::create($validated);

    return redirect()->route('permohonan.sukses')->with('nomor_registrasi', $validated['nomor_registrasi']);
})->name('permohonan.store');

Route::get('/permohonan/berhasil', function () {
    if (!session('nomor_registrasi')) {
        return redirect()->route('permohonan.create');
    }
    return view('permohonan_sukses', ['nomor_registrasi' => session('nomor_registrasi')]);
})->name('permohonan.sukses');

Route::get('/permohonan/tanda-terima/{nomor_registrasi}', function ($nomor_registrasi) {
    $permohonan = \App\Models\PermohonanInformasi::where('nomor_registrasi', $nomor_registrasi)->firstOrFail();
    return view('permohonan_tanda_terima', compact('permohonan'));
})->name('permohonan.tanda_terima');

Route::get('/lacak', function (\Illuminate\Http\Request $request) {
    $permohonan = null;
    $searched = false;
    
    if ($request->has('tracking_id')) {
        $searched = true;
        $permohonan = \App\Models\PermohonanInformasi::with('logs')->where('nomor_registrasi', $request->tracking_id)->first();
    }
    
    return view('lacak', compact('permohonan', 'searched'));
})->name('permohonan.lacak');

Route::prefix('admin')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'authenticate'])->name('admin.authenticate');
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth')->group(function () {
        // 2FA Routes
        Route::get('/2fa', [\App\Http\Controllers\Admin\TwoFactorChallengeController::class, 'show'])->name('admin.2fa.challenge');
        Route::post('/2fa', [\App\Http\Controllers\Admin\TwoFactorChallengeController::class, 'verify'])->name('admin.2fa.verify');
        Route::get('/profile/2fa', [\App\Http\Controllers\Admin\TwoFactorController::class, 'index'])->name('admin.2fa.setup');
        Route::post('/profile/2fa/enable', [\App\Http\Controllers\Admin\TwoFactorController::class, 'enable'])->name('admin.2fa.enable');
        Route::post('/profile/2fa/disable', [\App\Http\Controllers\Admin\TwoFactorController::class, 'disable'])->name('admin.2fa.disable');

        // Protected by 2FA (Temporarily disabled)
        Route::middleware([])->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
            
            // Permohonan routes
            Route::get('/permohonan', [\App\Http\Controllers\Admin\PermohonanController::class, 'index'])->name('admin.permohonan.index');
            Route::get('/permohonan/{id}', [\App\Http\Controllers\Admin\PermohonanController::class, 'show'])->name('admin.permohonan.show');
            Route::post('/permohonan/{id}/status', [\App\Http\Controllers\Admin\PermohonanController::class, 'updateStatus'])->name('admin.permohonan.update-status');
            Route::post('/permohonan/{id}/assign', [\App\Http\Controllers\Admin\PermohonanController::class, 'assignPetugas'])->name('admin.permohonan.assign');
            Route::post('/permohonan/{id}/extend-deadline', [\App\Http\Controllers\Admin\PermohonanController::class, 'extendDeadline'])->name('admin.permohonan.extend-deadline');
            Route::post('/penugasan/{id}/submit-data', [\App\Http\Controllers\Admin\PermohonanController::class, 'submitData'])->name('admin.penugasan.submit-data');
            Route::post('/penugasan/{id}/review', [\App\Http\Controllers\Admin\PermohonanController::class, 'reviewPenugasan'])->name('admin.penugasan.review');

            // Keberatan routes
            Route::get('/keberatan', [\App\Http\Controllers\Admin\KeberatanController::class, 'index'])->name('admin.keberatan.index');
            Route::get('/keberatan/{id}', [\App\Http\Controllers\Admin\KeberatanController::class, 'show'])->name('admin.keberatan.show');
            Route::post('/permohonan/{id}/keberatan', [\App\Http\Controllers\Admin\KeberatanController::class, 'store'])->name('admin.keberatan.store');
            Route::post('/keberatan/{id}/status', [\App\Http\Controllers\Admin\KeberatanController::class, 'updateStatus'])->name('admin.keberatan.update-status');

            Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('admin.profile.index');
            Route::post('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');

            // Super Admin Modules
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class, ['as' => 'admin']);
            Route::post('/users/{user}/reset-2fa', [\App\Http\Controllers\Admin\UserController::class, 'reset2fa'])->name('admin.users.reset-2fa');
            
            Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class, ['as' => 'admin']);
            
            Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
            Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
            
            Route::get('/logs', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('admin.logs.index');

            // Master Data routes
            Route::resource('unit-pengolah', \App\Http\Controllers\Admin\UnitPengolahController::class, [
                'as' => 'admin'
            ]);
            Route::resource('klasifikasi-arsip', \App\Http\Controllers\Admin\KlasifikasiArsipController::class, [
                'as' => 'admin'
            ]);
            Route::resource('kategori-pemohon', \App\Http\Controllers\Admin\KategoriPemohonController::class, [
                'as' => 'admin'
            ]);
            Route::resource('cara-memperoleh-informasi', \App\Http\Controllers\Admin\CaraMemperolehInformasiController::class, [
                'as' => 'admin'
            ]);
            Route::resource('kategori-informasi-publik', \App\Http\Controllers\Admin\KategoriInformasiPublikController::class, [
                'as' => 'admin'
            ]);

            // E-Office Persuratan routes
            Route::resource('surat-masuk', \App\Http\Controllers\Admin\SuratMasukController::class, [
                'as' => 'admin'
            ]);
            Route::post('/surat-masuk/{surat_masuk}/disposisi', [\App\Http\Controllers\Admin\SuratMasukController::class, 'disposisi'])->name('admin.surat-masuk.disposisi');

            Route::resource('surat-keluar', \App\Http\Controllers\Admin\SuratKeluarController::class, [
                'as' => 'admin'
            ]);
            Route::post('/surat-keluar/{surat_keluar}/approve-tte', [\App\Http\Controllers\Admin\SuratKeluarController::class, 'approveTte'])->name('admin.surat-keluar.approve-tte');
        });
    });
});
