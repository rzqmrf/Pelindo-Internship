<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $isMaintenance = SystemSetting::getBool('maintenance_mode');
            $isImpersonating = $request->session()->has('impersonator_id');
            $configuredBypass = (string) SystemSetting::getValue('maintenance_bypass_token', '');
            $hasBypass = $configuredBypass !== '' && hash_equals($configuredBypass, (string) $request->session()->get('maintenance_bypass_token', ''));

            if ($isMaintenance && !$user->isSuperAdmin() && !$isImpersonating && !$hasBypass) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Sistem sedang dalam mode pemeliharaan. Silakan masuk kembali setelah pemeliharaan selesai.',
                ]);
            }

            return redirect()->to($user->homeRoute());
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

                return back()->withErrors(['email' => 'Akun Anda sedang dinonaktifkan oleh administrator.']);
            }

            $configuredBypass = (string) SystemSetting::getValue('maintenance_bypass_token', '');
            $hasBypass = $configuredBypass !== '' && hash_equals($configuredBypass, (string) $request->session()->get('maintenance_bypass_token', ''));

            if (SystemSetting::getBool('maintenance_mode') && !$user->isSuperAdmin() && !$hasBypass) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Sistem sedang dalam mode pemeliharaan. Saat ini hanya Pengembang Sistem (Superadmin) yang dapat masuk.',
                ])->onlyInput('email');
            }

            AuditLogger::record(
                'auth.login',
                'auth',
                "Pengguna {$user->name} ({$user->role}) berhasil masuk ke sistem.",
                $user
            );

            return redirect()->intended($user->homeRoute());
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            AuditLogger::record(
                'auth.logout',
                'auth',
                "Pengguna {$user->name} ({$user->role}) keluar dari sistem.",
                $user
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah keluar dari sistem.');
    }
}
