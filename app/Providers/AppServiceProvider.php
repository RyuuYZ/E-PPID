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

        // Register custom secure_file validator
        \Illuminate\Support\Facades\Validator::extend('secure_file', function ($attribute, $value, $parameters, $validator) {
            if (!$value instanceof \Illuminate\Http\UploadedFile) {
                return false;
            }

            $allowed = !empty($parameters) ? $parameters : ['jpg', 'jpeg', 'png', 'pdf'];
            $maxKb = 5120;
            if (count($allowed) > 1 && is_numeric(end($allowed))) {
                $maxKb = (int) array_pop($allowed);
            }

            $error = app(\App\Services\FileSecurityService::class)->validateSecureFile($value, $allowed, $maxKb);
            if ($error !== null) {
                $validator->customMessages["{$attribute}.secure_file"] = $error;
                return false;
            }

            return true;
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
