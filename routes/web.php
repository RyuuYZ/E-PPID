<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/permohonan/baru', function () {
    return view('permohonan');
})->name('permohonan.create');

Route::post('/permohonan/simpan', function (\Illuminate\Http\Request $request) {
    // Controller logic will be implemented here
    return back()->with('success', 'Permohonan berhasil dikirim!');
})->name('permohonan.store');

Route::get('/lacak', function () {
    return view('lacak');
})->name('permohonan.lacak');
