<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorChallengeController extends Controller
{
    public function show()
    {
        if (!Auth::user()->google2fa_enabled) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.2fa-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required',
        ]);

        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            $request->session()->put('2fa_verified', true);
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['one_time_password' => 'Kode OTP tidak valid. Silakan coba lagi.']);
    }
}
