<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'supervisor' => redirect()->route('supervisor.dashboard'),
                'intern' => redirect()->route('intern.dashboard'),
                default => redirect()->route('login'),
            };
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda sedang dinonaktifkan oleh HR.']);
            }

            return match ($user->role) {
                'admin' => redirect()->intended(route('admin.dashboard')),
                'supervisor' => redirect()->intended(route('supervisor.dashboard')),
                'intern' => redirect()->intended(route('intern.dashboard')),
                default => redirect()->route('login'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah keluar dari sistem.');
    }
}
