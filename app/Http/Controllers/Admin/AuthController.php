<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the login form for admin.
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle the authentication process.
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
            'cf-turnstile-response' => ['required', new \App\Rules\TurnstileRule()],
        ]);

        $loginInput = $request->input('username');
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $request->input('password'),
        ];

        $throttleKey = Str::transliterate(Str::lower($request->input('username')).'|'.$request->ip());

        // Batasi maksimal 5 percobaan login
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akun Anda telah diblokir. Silakan hubungi administrator.',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();

            // Invalidate other sessions for this user
            Auth::logoutOtherDevices($credentials['password']);

            if ($user->must_change_password) {
                return redirect()->route('admin.force-change-password');
            }

            // Nanti bisa diarahkan berdasarkan role (Misal: Atasan, Pelaksana, Desk, dll)
            return redirect()->intended(route('admin.dashboard'));
        }

        // Catat kegagalan login (dikunci selama 1 menit/60 detik jika limit tercapai)
        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'username' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ])->onlyInput('username');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function showForceChangePassword()
    {
        if (!Auth::user()->must_change_password) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.auth.change-password');
    }

    public function processForceChangePassword(Request $request)
    {
        if (!Auth::user()->must_change_password) {
            return redirect()->route('admin.dashboard');
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed'
            ],
        ], [
            'password.regex' => 'Kata sandi harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial.',
        ]);

        $user = Auth::user();

        if (\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.'
            ]);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'must_change_password' => false
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Change Password',
            'description' => "Pengguna mengganti kata sandi wajib setelah login pertama.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
