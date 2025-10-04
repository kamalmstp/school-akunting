<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Kata sandi wajib diisi'
        ]);

        // Throttle login attempts (5 attempts per minute)
        $throttleKey = Str::lower($request->input('email') . '|' . $request->ip());
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->withInput();
        }

        // Attempt login
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Redirect berdasarkan peran
            $user = Auth::user();
            if ($user->status) {
                if ($user->role === 'SuperAdmin') {
                    return redirect()->route('dashboard', ['school_id' => null]);
                } elseif ($user->role === 'SchoolAdmin') {
                    return redirect()->route('dashboard.index', $user->school_id);
                } elseif ($user->role === 'AdminMonitor') {
                    return redirect()->route('schools.index');
                } elseif ($user->role === 'Pengawas') {
                    return redirect()->route('schools.index');
                }
            } else {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with([
                    'error' => 'Akun non aktif. Hubungi admin untuk bisa kembali masuk.',
                ]);
            }
        }

        // Increment throttle attempts
        RateLimiter::hit($throttleKey);

        return back()->withErrors([
            'email' => 'Email atau kata sandi salah.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}