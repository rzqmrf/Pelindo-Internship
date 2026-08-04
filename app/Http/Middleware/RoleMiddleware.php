<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda nonaktif. Silakan hubungi Admin HR Pelindo.']);
        }

        if (!in_array($user->role, $roles)) {
            // Redirect to appropriate dashboard based on their role
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'supervisor' => redirect()->route('supervisor.dashboard'),
                'intern' => redirect()->route('intern.dashboard'),
                default => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}
