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
    $validated['status'] = 'Diproses';
    $validated['tahapan_proses'] = 'Diterima';

    \App\Models\PermohonanInformasi::create($validated);

    return back()->with('success', 'Permohonan berhasil dikirim!');
})->name('permohonan.store');

Route::get('/lacak', function () {
    return view('lacak');
})->name('permohonan.lacak');

Route::prefix('admin')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'authenticate'])->name('admin.authenticate');
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Permohonan routes
        Route::get('/permohonan', [\App\Http\Controllers\Admin\PermohonanController::class, 'index'])->name('admin.permohonan.index');
        Route::get('/permohonan/{id}', [\App\Http\Controllers\Admin\PermohonanController::class, 'show'])->name('admin.permohonan.show');
        Route::post('/permohonan/{id}/status', [\App\Http\Controllers\Admin\PermohonanController::class, 'updateStatus'])->name('admin.permohonan.update-status');

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
