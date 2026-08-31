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

        if ($user && $user->google2fa_enabled) {
            if (!$request->session()->has('2fa_verified') || $request->session()->get('2fa_verified') !== true) {
                // Jangan blokir rute untuk verifikasi 2FA itu sendiri
                if (!$request->is('admin/2fa*')) {
                    return redirect()->route('admin.2fa.challenge');
                }
            }
        }

        return $next($request);
    }
}
