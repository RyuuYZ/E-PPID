<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        
        $secret = $user->google2fa_secret;

        if (!$secret) {
            $secret = $google2fa->generateSecretKey();
            $user->google2fa_secret = $secret;
            $user->save();
        }

        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('admin.auth.2fa-setup', compact('QR_Image', 'secret'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required',
        ]);

        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            $user->google2fa_enabled = true;
            $user->save();
            return redirect()->route('admin.dashboard')->with('success', 'Two-Factor Authentication berhasil diaktifkan.');
        }

        return back()->withErrors(['one_time_password' => 'Kode OTP tidak valid.']);
    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->google2fa_enabled = false;
        $user->save();

        return redirect()->route('admin.dashboard')->with('success', 'Two-Factor Authentication dinonaktifkan.');
    }
}
