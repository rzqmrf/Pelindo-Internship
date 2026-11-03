<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SystemMaintenanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = SystemSetting::allCached();

        $impersonator = null;
        if ($request->hasSession() && $request->session()->has('impersonator_id')) {
            $impersonatorId = (int) $request->session()->get('impersonator_id');
            $candidate = User::find($impersonatorId);
            if ($candidate && $candidate->isSuperAdmin() && $candidate->is_active) {
                $impersonator = $candidate;
            } else {
                $request->session()->forget('impersonator_id');
            }
        }

        $banner = [
            'enabled' => in_array((string) ($settings['banner_enabled'] ?? '0'), ['1', 'true', 'on'], true),
            'level' => $settings['banner_level'] ?? 'info',
            'title' => $settings['banner_title'] ?? '',
            'message' => $settings['banner_message'] ?? '',
            'schedule_at' => $settings['banner_schedule_at'] ?? '',
        ];

        $maintenance = [
            'enabled' => in_array((string) ($settings['maintenance_mode'] ?? '0'), ['1', 'true', 'on'], true),
            'title' => $settings['maintenance_title'] ?? 'Pemeliharaan Sistem Terjadwal',
            'message' => $settings['maintenance_message'] ?? '',
            'eta' => $settings['maintenance_eta'] ?? '',
            'contact' => $settings['maintenance_contact'] ?? 'it.helpdesk@pelindo.co.id',
        ];

        View::share('globalSystemBanner', $banner);
        View::share('globalMaintenanceState', $maintenance);
        View::share('activeImpersonator', $impersonator);

        if ($maintenance['enabled']) {
            $user = Auth::user();
            $configuredBypassToken = (string) ($settings['maintenance_bypass_token'] ?? '');
            $sessionBypassToken = $request->hasSession() ? (string) $request->session()->get('maintenance_bypass_token', '') : '';
            $hasValidTokenBypass = $configuredBypassToken !== '' && hash_equals($configuredBypassToken, $sessionBypassToken);

            $hasSuperAdminBypass = ($user && $user->isSuperAdmin()) || $impersonator !== null || $hasValidTokenBypass;

            $allowCertVerify = in_array((string) ($settings['maintenance_allow_cert_verify'] ?? '1'), ['1', 'true', 'on'], true);
            $allowedRoutesForPublic = $request->is('login', 'logout', 'manifest.json', 'impersonate/leave', 'maintenance-bypass/*')
                || ($allowCertVerify && $request->is('verify-certificate/*', 'certificate/download/*'));

            if (!$hasSuperAdminBypass && !$allowedRoutesForPublic) {
                return response()->view('errors.maintenance', [
                    'maintenance' => $maintenance,
                ], 503);
            }
        }

        return $next($request);
    }
}
