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
    if (!$request->has('subjek_informasi') && $request->has('subjek')) {
        $request->merge(['subjek_informasi' => $request->input('subjek')]);
    }

    if ($request->filled('kecamatan') && $request->filled('desa')) {
        $detail = trim($request->input('detail_alamat', ''));
        $alamat = ($detail !== '' ? $detail . ', ' : '') . 'Desa/Kel. ' . $request->input('desa') . ', Kec. ' . $request->input('kecamatan') . ', Kab. Ciamis, Jawa Barat';
        $request->merge(['alamat' => $alamat]);
    }

    $validated = $request->validate([
        'nama_pemohon' => 'required|string|max:255',
        'kategori_pemohon_id' => 'required|exists:kategori_pemohons,id',
        'nik_atau_no_badan_hukum' => ['required', 'regex:/^[0-9]{1,16}$/'],
        'no_telp' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'alamat' => 'required|string',
        'subjek_informasi' => 'required|string|max:255',
        'rincian_informasi' => 'required|string',
        'tujuan_penggunaan' => 'required|string',
        'cara_memperoleh_informasi_id' => 'required|exists:cara_memperoleh_informasis,id',
        'file_identitas' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ], [
        'nik_atau_no_badan_hukum.required' => 'NIK / No. Identitas wajib diisi.',
        'nik_atau_no_badan_hukum.regex' => 'NIK harus berupa angka dan tidak boleh lebih dari 16 angka.',
        'subjek_informasi.required' => 'Judul / Subjek informasi wajib diisi.',
        'rincian_informasi.required' => 'Rincian / Isi informasi wajib diisi.',
        'file_identitas.mimes' => 'Format file identitas harus berupa gambar (JPG, JPEG, PNG) atau dokumen (PDF). Anda mencoba mengunggah format yang tidak diizinkan.',
        'file_identitas.max' => 'Ukuran file identitas maksimal adalah 5MB.',
        'file_identitas.file' => 'File identitas harus berupa file yang valid.'
    ]);

    if ($request->hasFile('file_identitas')) {
        $path = $request->file('file_identitas')->store('identitas', 'local');
        $validated['file_identitas'] = $path;
    }

    $validated['nomor_registrasi'] = 'REG-' . date('YmdHis') . '-' . rand(1000, 9999);
    $validated['status'] = \App\Enums\PermohonanStatus::Diajukan->value;

    $permohonan = \App\Models\PermohonanInformasi::create($validated);

    // Kirim kode invoice / pendaftaran ke email pemohon
    try {
        \Illuminate\Support\Facades\Mail::to($permohonan->email)->send(new \App\Mail\PermohonanTerkirimMail($permohonan));
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Gagal mengirim email permohonan: ' . $e->getMessage());
    }

    return redirect()->route('permohonan.sukses')->with([
        'nomor_registrasi' => $permohonan->nomor_registrasi,
        'email' => $permohonan->email,
    ]);
})->name('permohonan.store')->middleware('throttle:5,1');

Route::get('/permohonan/berhasil', function () {
    if (!session('nomor_registrasi')) {
        return redirect()->route('permohonan.create');
    }
    return view('permohonan_sukses', [
        'nomor_registrasi' => session('nomor_registrasi'),
        'email' => session('email'),
    ]);
})->name('permohonan.sukses');

Route::get('/permohonan/tanda-terima/{nomor_registrasi}', function ($nomor_registrasi) {
    $permohonan = \App\Models\PermohonanInformasi::where('nomor_registrasi', $nomor_registrasi)->firstOrFail();
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('permohonan_tanda_terima', compact('permohonan'));
    return $pdf->download('Tanda_Terima_Permohonan_' . $permohonan->nomor_registrasi . '.pdf');
})->name('permohonan.tanda_terima');

Route::get('/lacak', function (\Illuminate\Http\Request $request) {
    $permohonan = null;
    $searched = false;
    
    if ($request->has('tracking_id')) {
        $searched = true;
        $permohonan = \App\Models\PermohonanInformasi::with(['logs', 'keberatan'])->where('nomor_registrasi', $request->tracking_id)->first();
    }
    
    return view('lacak', compact('permohonan', 'searched'));
})->name('permohonan.lacak');

// Public Daftar Informasi Publik (DIP) Routes
Route::get('/informasi-publik', [\App\Http\Controllers\InformasiPublikPublicController::class, 'index'])->name('informasi-publik.index');
Route::get('/informasi-publik/download/{id}', [\App\Http\Controllers\InformasiPublikPublicController::class, 'download'])->name('informasi-publik.download');

// Public Keberatan Submission
Route::post('/permohonan/{nomor_registrasi}/keberatan', [\App\Http\Controllers\KeberatanPublicController::class, 'store'])->name('permohonan.keberatan.store');

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
        Route::get('/change-password', [\App\Http\Controllers\Admin\AuthController::class, 'showForceChangePassword'])->name('admin.force-change-password');
        Route::post('/change-password', [\App\Http\Controllers\Admin\AuthController::class, 'processForceChangePassword'])->name('admin.process-force-change-password');

        Route::middleware(['force_password_change'])->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
            
            // Permohonan routes
            Route::get('/permohonan', [\App\Http\Controllers\Admin\PermohonanController::class, 'index'])->name('admin.permohonan.index');
            Route::get('/permohonan/create', [\App\Http\Controllers\Admin\PermohonanController::class, 'create'])->name('admin.permohonan.create');
            Route::post('/permohonan', [\App\Http\Controllers\Admin\PermohonanController::class, 'store'])->name('admin.permohonan.store');
            Route::get('/permohonan/{id}', [\App\Http\Controllers\Admin\PermohonanController::class, 'show'])->name('admin.permohonan.show');
            Route::get('/permohonan/{id}/file-identitas', [\App\Http\Controllers\Admin\PermohonanController::class, 'viewFileIdentitas'])->name('admin.permohonan.file-identitas');
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
            Route::post('/profile/signature', [\App\Http\Controllers\Admin\ProfileController::class, 'updateSignature'])->name('admin.profile.signature');

            // Super Admin Modules
            Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
            Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
            Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
            Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
            Route::post('/users/{user}/reset-2fa', [\App\Http\Controllers\Admin\UserController::class, 'reset2fa'])->name('admin.users.reset-2fa');
            Route::post('/users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('admin.users.toggle-active');
            
            Route::get('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('admin.roles.index');
            Route::post('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('admin.roles.store');
            Route::put('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('admin.roles.update');
            Route::delete('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('admin.roles.destroy');
            
            Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
            Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
            
            Route::get('/logs', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('admin.logs.index');

            // Master Data Unified Route & Resource Endpoints
            Route::get('/master-data', [\App\Http\Controllers\Admin\MasterDataController::class, 'index'])->name('admin.master-data.index');
            Route::resource('unit-pengolah', \App\Http\Controllers\Admin\UnitPengolahController::class, [
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
            Route::resource('informasi-publik', \App\Http\Controllers\Admin\InformasiPublikController::class, [
                'as' => 'admin'
            ]);
        });
    });
});
