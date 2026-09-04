<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermohonanController;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/
Route::get('/v1/referensi', [PermohonanController::class, 'referensi']);
Route::post('/v1/permohonan', [PermohonanController::class, 'store']);
Route::get('/v1/permohonan/lacak/{no_registrasi}', [PermohonanController::class, 'lacak']);

/*
|--------------------------------------------------------------------------
| Authentication Route
|--------------------------------------------------------------------------
*/
Route::post('/v1/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Internal Admin API Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('admin/permohonan')->group(function () {
        Route::get('/', [PermohonanController::class, 'indexAdmin']);
        Route::get('/{id}', [PermohonanController::class, 'showAdmin']);
        Route::post('/{id}/transition', [PermohonanController::class, 'transitionAdmin']);
    });
});
