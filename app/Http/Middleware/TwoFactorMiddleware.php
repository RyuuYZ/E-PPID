<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            // Jika 2FA sudah aktif, pastikan OTP diverifikasi
            if ($user->google2fa_enabled) {
                if (!$request->session()->has('2fa_verified') || $request->session()->get('2fa_verified') !== true) {
                    if (!$request->is('admin/2fa*')) {
                        return redirect()->route('admin.2fa.challenge');
                    }
                }
            } else {
                // Jika 2FA BELUM aktif, paksa mereka ke halaman setup
                if (!$request->is('admin/profile/2fa*')) {
                    return redirect()->route('admin.2fa.setup');
                }
            }
        }

        return $next($request);
    }
}
