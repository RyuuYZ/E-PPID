<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        // View Composer for Admin Notification Center
        \Illuminate\Support\Facades\View::composer('admin.layouts.app', function ($view) {
            try {
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $notifPermohonan = \App\Models\PermohonanInformasi::where('status', \App\Enums\PermohonanStatus::Diajukan)
                        ->latest()
                        ->take(5)
                        ->get();

                    $notifKeberatan = \App\Models\PengajuanKeberatan::with('permohonan_informasi')
                        ->where('status', \App\Enums\KeberatanStatus::Masuk)
                        ->latest()
                        ->take(3)
                        ->get();

                    $countPermohonan = \App\Models\PermohonanInformasi::where('status', \App\Enums\PermohonanStatus::Diajukan)->count();
                    $countKeberatan = \App\Models\PengajuanKeberatan::where('status', \App\Enums\KeberatanStatus::Masuk)->count();
                    $total = $countPermohonan + $countKeberatan;

                    $view->with([
                        'headerNotifPermohonan' => $notifPermohonan,
                        'headerNotifKeberatan' => $notifKeberatan,
                        'headerTotalNotif' => $total,
                    ]);
                }
            } catch (\Throwable $e) {
                // Ignore if DB not ready
            }
        });
    }
}
