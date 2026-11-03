<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AuditLogger
{
    public static function record(
        string $action,
        string $module,
        string $description,
        ?Model $target = null,
        array $metadata = [],
        ?int $explicitUserId = null
    ): void {
        try {
            if (!Schema::hasTable('audit_logs')) {
                return;
            }

            $request = request();

            AuditLog::create([
                'user_id' => $explicitUserId ?? Auth::id(),
                'impersonator_id' => $request?->hasSession() ? $request->session()->get('impersonator_id') : null,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'target_type' => $target ? class_basename($target) : null,
                'target_id' => $target?->getKey(),
                'metadata' => !empty($metadata) ? $metadata : null,
                'ip_address' => $request?->ip(),
                'user_agent' => $request ? substr((string) $request->userAgent(), 0, 500) : null,
            ]);
        } catch (\Throwable $e) {
            // Fail gracefully so business flow is never interrupted by audit write failure
        }
    }
}
