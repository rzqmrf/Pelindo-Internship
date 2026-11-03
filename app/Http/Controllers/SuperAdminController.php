<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Division;
use App\Models\InternProfile;
use App\Models\Role;
use App\Models\SupervisorProfile;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\AuditLogger;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $countsBySlug = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $roleCounts = [
            'superadmin' => (int) ($countsBySlug['superadmin'] ?? 0),
            'admin' => (int) ($countsBySlug['admin'] ?? 0),
            'supervisor' => (int) ($countsBySlug['supervisor'] ?? 0),
            'intern' => (int) ($countsBySlug['intern'] ?? 0),
            'inactive' => User::where('is_active', false)->count(),
        ];

        $rolesSummary = Role::allCached()->map(function (Role $role) use ($countsBySlug) {
            return [
                'slug' => $role->slug,
                'name' => $role->name,
                'description' => $role->description,
                'badge_classes' => $role->badgeClasses(),
                'is_system' => $role->is_system,
                'count' => (int) ($countsBySlug[$role->slug] ?? 0),
            ];
        });

        $health = $this->collectSystemHealth();
        $settings = SystemSetting::allCached();
        $recentAudits = AuditLog::with(['user', 'impersonator'])
            ->latest()
            ->take(8)
            ->get();

        return view('superadmin.dashboard', compact(
            'roleCounts',
            'rolesSummary',
            'health',
            'settings',
            'recentAudits'
        ));
    }

    public function maintenance()
    {
        $settings = SystemSetting::allCached();
        if (empty($settings['maintenance_bypass_token'])) {
            $generated = Str::random(32);
            SystemSetting::setMany([
                'maintenance_bypass_token' => ['value' => $generated, 'type' => 'string'],
            ], Auth::id());
            $settings = SystemSetting::allCached();
        }

        $bypassUrl = route('maintenance.bypass', ['token' => $settings['maintenance_bypass_token']]);
        $lastSettingUpdate = SystemSetting::with('updater')->latest('updated_at')->first();

        return view('superadmin.maintenance', compact('settings', 'lastSettingUpdate', 'bypassUrl'));
    }

    public function updateMaintenance(Request $request)
    {
        $section = $request->input('section', 'all');

        if ($section === 'banner') {
            $validated = $request->validate([
                'banner_enabled' => 'nullable|boolean',
                'banner_level' => 'required|in:info,warning,critical',
                'banner_title' => 'nullable|string|max:120',
                'banner_message' => 'nullable|string|max:1000',
                'banner_schedule_at' => 'nullable|string|max:100',
            ]);

            $enabled = $request->boolean('banner_enabled') ? '1' : '0';
            if ($enabled === '1' && blank($validated['banner_message'] ?? '')) {
                return back()->withInput()->withErrors([
                    'banner_message' => 'Isi pesan pemberitahuan wajib diisi saat banner diaktifkan.',
                ]);
            }

            SystemSetting::setMany([
                'banner_enabled' => ['value' => $enabled, 'type' => 'boolean'],
                'banner_level' => ['value' => $validated['banner_level'], 'type' => 'string'],
                'banner_title' => ['value' => $validated['banner_title'] ?? '', 'type' => 'string'],
                'banner_message' => ['value' => $validated['banner_message'] ?? '', 'type' => 'text'],
                'banner_schedule_at' => ['value' => $validated['banner_schedule_at'] ?? '', 'type' => 'string'],
            ], Auth::id());

            $statusLabel = $enabled === '1' ? 'diaktifkan' : 'dinonaktifkan';
            AuditLogger::record(
                'banner.update',
                'maintenance',
                "Banner pemberitahuan sistem {$statusLabel} (level: {$validated['banner_level']}).",
                null,
                ['banner_enabled' => $enabled, 'level' => $validated['banner_level']]
            );

            return redirect()->route('superadmin.maintenance')->with('success', "Pengaturan banner pemberitahuan berhasil {$statusLabel}.");
        }

        $validated = $request->validate([
            'maintenance_mode' => 'nullable|boolean',
            'maintenance_allow_cert_verify' => 'nullable|boolean',
            'maintenance_title' => 'required|string|max:150',
            'maintenance_message' => 'required|string|max:1500',
            'maintenance_eta' => 'nullable|date',
            'maintenance_contact' => 'nullable|string|max:150',
        ]);

        $enabled = $request->boolean('maintenance_mode') ? '1' : '0';
        $allowCertVerify = $request->boolean('maintenance_allow_cert_verify') ? '1' : '0';

        SystemSetting::setMany([
            'maintenance_mode' => ['value' => $enabled, 'type' => 'boolean'],
            'maintenance_allow_cert_verify' => ['value' => $allowCertVerify, 'type' => 'boolean'],
            'maintenance_title' => ['value' => $validated['maintenance_title'], 'type' => 'string'],
            'maintenance_message' => ['value' => $validated['maintenance_message'], 'type' => 'text'],
            'maintenance_eta' => ['value' => $validated['maintenance_eta'] ?? '', 'type' => 'datetime'],
            'maintenance_contact' => ['value' => $validated['maintenance_contact'] ?? 'it.helpdesk@pelindo.co.id', 'type' => 'string'],
        ], Auth::id());

        $statusLabel = $enabled === '1' ? 'DIAKTIFKAN' : 'DINONAKTIFKAN';
        AuditLogger::record(
            'maintenance.toggle',
            'maintenance',
            "Mode Pemeliharaan Penuh (Maintenance Mode) {$statusLabel}.",
            null,
            ['maintenance_mode' => $enabled, 'eta' => $validated['maintenance_eta'] ?? null]
        );

        return redirect()->route('superadmin.maintenance')->with(
            'success',
            $enabled === '1'
                ? 'Mode Pemeliharaan Aktif. Seluruh akses pengguna non-superadmin kini dialihkan ke halaman pemeliharaan.'
                : 'Mode Pemeliharaan dimatikan. Sistem kini dapat diakses normal kembali oleh seluruh pengguna.'
        );
    }

    public function regenerateMaintenanceBypass()
    {
        $newToken = Str::random(32);
        SystemSetting::setMany([
            'maintenance_bypass_token' => ['value' => $newToken, 'type' => 'string'],
        ], Auth::id());

        AuditLogger::record(
            'maintenance.regenerate_bypass',
            'maintenance',
            'Superadmin memperbarui tautan rahasia bypass pemeliharaan (token lama otomatis hangus).'
        );

        return redirect()->route('superadmin.maintenance')->with('success', 'Tautan bypass pemeliharaan berhasil diacak ulang. Tautan lama tidak berlaku lagi.');
    }

    public function activateMaintenanceBypass(Request $request, string $token)
    {
        $configuredToken = (string) SystemSetting::getValue('maintenance_bypass_token', '');

        if ($configuredToken === '' || !hash_equals($configuredToken, $token)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Tautan pratinjau bypass pemeliharaan tidak valid atau telah dicabut oleh Superadmin.',
            ]);
        }

        $request->session()->put('maintenance_bypass_token', $token);

        AuditLogger::record(
            'maintenance.bypass_activated',
            'maintenance',
            'Akses pratinjau bypass pemeliharaan diaktifkan pada sesi peramban.'
        );

        if (Auth::check()) {
            return redirect()->to(Auth::user()->homeRoute())->with('success', 'Akses pratinjau selama pemeliharaan telah aktif.');
        }

        return redirect()->route('login')->with('status', 'Akses pratinjau pemeliharaan aktif pada peramban ini. Silakan masuk dengan akun Anda.');
    }

    public function optimizeSystem(Request $request)
    {
        $request->validate([
            'action' => 'required|in:optimize_clear,cache_clear,view_clear,storage_link',
        ]);

        $action = $request->input('action');
        $message = '';

        try {
            switch ($action) {
                case 'optimize_clear':
                    Artisan::call('optimize:clear');
                    SystemSetting::flushCache();
                    Role::flushCache();
                    $message = 'Seluruh cache konfigurasi, rute, view, dan aplikasi berhasil dibersihkan (optimize:clear).';
                    break;

                case 'cache_clear':
                    Artisan::call('cache:clear');
                    SystemSetting::flushCache();
                    Role::flushCache();
                    $message = 'Cache data aplikasi, role, dan pengaturan sistem berhasil disegarkan.';
                    break;

                case 'view_clear':
                    Artisan::call('view:clear');
                    $message = 'Kompilasi template Blade (view cache) berhasil dibersihkan.';
                    break;

                case 'storage_link':
                    if (file_exists(public_path('storage'))) {
                        $message = 'Symbolic link public/storage sudah terpasang dan aktif.';
                    } else {
                        Artisan::call('storage:link');
                        $message = 'Symbolic link public/storage berhasil dibuat ulang.';
                    }
                    break;
            }

            AuditLogger::record(
                "system.{$action}",
                'system',
                "Menjalankan utilitas sistem: {$action}."
            );

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menjalankan perintah sistem: ' . $e->getMessage());
        }
    }

    public function users(Request $request)
    {
        Role::ensureDefaults();
        $roles = Role::allCached();

        $query = User::with(['internProfile.division', 'supervisorProfile.division']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderByRaw("CASE role WHEN 'superadmin' THEN 1 WHEN 'admin' THEN 2 WHEN 'supervisor' THEN 3 WHEN 'intern' THEN 4 ELSE 2 END")
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $divisions = Division::orderBy('name')->get();
        $countsBySlug = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $counts = [
            'all' => User::count(),
            'superadmin' => (int) ($countsBySlug['superadmin'] ?? 0),
            'admin' => (int) ($countsBySlug['admin'] ?? 0),
            'supervisor' => (int) ($countsBySlug['supervisor'] ?? 0),
            'intern' => (int) ($countsBySlug['intern'] ?? 0),
        ];

        $roleTabs = [
            '' => ['label' => 'Semua Akun', 'count' => $counts['all']],
        ];
        foreach ($roles as $roleObj) {
            $roleTabs[$roleObj->slug] = [
                'label' => $roleObj->name,
                'count' => (int) ($countsBySlug[$roleObj->slug] ?? 0),
            ];
        }

        $rolesPortalMap = $roles->pluck('base_portal', 'slug')->toArray();

        return view('superadmin.users', compact('users', 'divisions', 'counts', 'roles', 'roleTabs', 'rolesPortalMap'));
    }

    public function storeUser(Request $request)
    {
        Role::ensureDefaults();
        $allowedRoles = Role::allCached()->pluck('slug')->all();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'division_id' => 'nullable|exists:divisions,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $defaultDivisionId = $validated['division_id'] ?? Division::first()?->id;
        $basePortal = $user->basePortal();

        if ($basePortal === 'supervisor' && $defaultDivisionId) {
            SupervisorProfile::create([
                'user_id' => $user->id,
                'division_id' => $defaultDivisionId,
                'employee_id_number' => 'NIP-' . now()->format('YmdHis'),
                'position_title' => 'Pembimbing Lapangan',
                'phone' => $user->phone,
            ]);
        } elseif ($basePortal === 'intern' && $defaultDivisionId) {
            $defaultSpv = User::where('role', 'supervisor')->first();
            InternProfile::create([
                'user_id' => $user->id,
                'division_id' => $defaultDivisionId,
                'supervisor_id' => $defaultSpv?->id ?? $user->id,
                'student_id_number' => 'REG-' . now()->format('His'),
                'institution' => 'Belum Dilengkapi',
                'major' => 'Belum Dilengkapi',
                'start_date' => Carbon::today(),
                'end_date' => Carbon::today()->addMonths(3),
                'status' => 'active',
            ]);
        }

        AuditLogger::record(
            'user.create',
            'users',
            "Superadmin membuat akun baru {$user->name} ({$user->email}) dengan role {$user->roleLabel()} ({$user->role}).",
            $user
        );

        return redirect()->route('superadmin.users')->with('success', "Akun {$user->name} ({$user->roleLabel()}) berhasil dibuat.");
    }

    public function updateUser(Request $request, $id)
    {
        Role::ensureDefaults();
        $target = User::findOrFail($id);
        $allowedRoles = Role::allCached()->pluck('slug')->all();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$target->id}",
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        $newRoleDef = Role::findBySlug($validated['role']);
        $newRoleIsSuperAdmin = $validated['role'] === 'superadmin' || ($newRoleDef && $newRoleDef->base_portal === 'superadmin');

        if ($target->id === Auth::id() && !$newRoleIsSuperAdmin) {
            return back()->with('error', 'Anda tidak dapat menurunkan role akun Superadmin yang sedang Anda gunakan.');
        }

        if ($target->isSuperAdmin() && !$newRoleIsSuperAdmin) {
            $superAdminSlugs = Role::allCached()->where('base_portal', 'superadmin')->pluck('slug')->push('superadmin')->unique()->all();
            $activeSuperAdmins = User::whereIn('role', $superAdminSlugs)->where('is_active', true)->count();
            if ($activeSuperAdmins <= 1) {
                return back()->with('error', 'Tidak dapat menurunkan satu-satunya akun Superadmin aktif di sistem.');
            }
        }

        $oldRole = $target->role;

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $target->update($payload);

        AuditLogger::record(
            'user.update',
            'users',
            "Superadmin memperbarui akun {$target->name} (role: {$oldRole} -> {$target->role})" . (!empty($validated['password']) ? ' beserta reset kata sandi.' : '.'),
            $target
        );

        return redirect()->route('superadmin.users')->with('success', "Data akun {$target->name} berhasil diperbarui.");
    }

    public function toggleUserStatus($id)
    {
        $target = User::findOrFail($id);

        if ($target->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        if ($target->isSuperAdmin() && $target->is_active) {
            $superAdminSlugs = Role::allCached()->where('base_portal', 'superadmin')->pluck('slug')->push('superadmin')->unique()->all();
            $activeSuperAdmins = User::whereIn('role', $superAdminSlugs)->where('is_active', true)->count();
            if ($activeSuperAdmins <= 1) {
                return back()->with('error', 'Tidak dapat menonaktifkan satu-satunya Superadmin aktif di sistem.');
            }
        }

        $target->update(['is_active' => !$target->is_active]);
        $stateText = $target->is_active ? 'diaktifkan' : 'dinonaktifkan';

        AuditLogger::record(
            'user.toggle_status',
            'users',
            "Akun {$target->name} ({$target->role}) {$stateText} oleh Superadmin.",
            $target
        );

        return back()->with('success', "Status akun {$target->name} berhasil {$stateText}.");
    }

    public function deleteUser($id)
    {
        $target = User::findOrFail($id);

        if ($target->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($target->isSuperAdmin()) {
            $superAdminSlugs = Role::allCached()->where('base_portal', 'superadmin')->pluck('slug')->push('superadmin')->unique()->all();
            $superAdminCount = User::whereIn('role', $superAdminSlugs)->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus akun Superadmin terakhir di sistem.');
            }
        }

        $name = $target->name;
        $role = $target->role;
        $target->delete();

        AuditLogger::record(
            'user.delete',
            'users',
            "Superadmin menghapus permanen akun {$name} ({$role})."
        );

        return back()->with('success', "Akun {$name} berhasil dihapus dari sistem.");
    }

    public function impersonate(Request $request, $id)
    {
        $superAdmin = Auth::user();
        $target = User::findOrFail($id);

        if ($target->id === $superAdmin->id) {
            return back()->with('error', 'Anda sudah masuk menggunakan akun ini.');
        }

        if (!$target->is_active) {
            return back()->with('error', 'Akun yang nonaktif tidak dapat di-impersonasi. Aktifkan akun terlebih dahulu.');
        }

        $request->session()->put('impersonator_id', $superAdmin->id);

        AuditLogger::record(
            'impersonate.start',
            'impersonation',
            "Superadmin {$superAdmin->name} memulai sesi impersonasi masuk sebagai {$target->name} ({$target->role}).",
            $target,
            ['target_email' => $target->email],
            $superAdmin->id
        );

        Auth::login($target);

        return redirect()->to($target->homeRoute())->with('success', "Mode Impersonasi aktif: Anda kini melihat sistem sebagai {$target->name} ({$target->roleLabel()}).");
    }

    public function leaveImpersonation(Request $request)
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('login');
        }

        $currentUser = Auth::user();
        $superAdmin = User::find($impersonatorId);

        if (!$superAdmin || !$superAdmin->isSuperAdmin() || !$superAdmin->is_active) {
            Auth::logout();

            return redirect()->route('login')->withErrors(['email' => 'Sesi Superadmin asal tidak valid.']);
        }

        AuditLogger::record(
            'impersonate.stop',
            'impersonation',
            "Superadmin {$superAdmin->name} mengakhiri sesi impersonasi dari akun " . ($currentUser?->name ?? 'pengguna') . '.',
            $currentUser,
            [],
            $superAdmin->id
        );

        Auth::login($superAdmin);

        return redirect()->route('superadmin.users')->with('success', 'Anda telah kembali ke sesi Superadmin.');
    }

    public function roles()
    {
        Role::ensureDefaults();

        $roles = Role::orderByRaw("CASE slug WHEN 'superadmin' THEN 1 WHEN 'admin' THEN 2 WHEN 'supervisor' THEN 3 WHEN 'intern' THEN 4 ELSE 5 END")
            ->orderBy('name')
            ->get();

        $userCountsByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $permissionCatalog = Role::permissionCatalog();
        $portalOptions = Role::portalOptions();
        $badgeOptions = Role::badgeOptions();

        return view('superadmin.roles', compact(
            'roles',
            'userCountsByRole',
            'permissionCatalog',
            'portalOptions',
            'badgeOptions'
        ));
    }

    public function storeRole(Request $request)
    {
        Role::ensureDefaults();

        $rawSlug = $request->input('slug') ?: $request->input('name', '');
        $normalizedSlug = Str::slug((string) $rawSlug, '_');
        $request->merge(['slug' => $normalizedSlug]);

        $catalogKeys = array_keys(Role::permissionCatalog());

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|min:2|max:50|regex:/^[a-z0-9_]+$/|unique:roles,slug',
            'description' => 'nullable|string|max:255',
            'base_portal' => ['required', Rule::in(array_keys(Role::portalOptions()))],
            'badge_color' => ['required', Rule::in(array_keys(Role::badgeOptions()))],
            'permissions' => 'nullable|array',
            'permissions.*' => ['string', Rule::in($catalogKeys)],
        ], [
            'slug.unique' => 'Kode role (slug) tersebut sudah digunakan. Gunakan kode unik lain.',
            'slug.regex' => 'Kode role hanya boleh menggunakan huruf kecil, angka, dan garis bawah (_).',
        ]);

        if ($validated['base_portal'] === 'admin' && empty($validated['permissions'])) {
            return back()->withInput()->withErrors([
                'permissions' => 'Pilih minimal satu hak akses modul untuk role berbasis Portal Manajemen / Admin.',
            ]);
        }

        $permissions = match ($validated['base_portal']) {
            'superadmin' => ['*'],
            'admin' => array_values(array_unique($validated['permissions'] ?? [])),
            default => [],
        };

        $role = Role::create([
            'slug' => $validated['slug'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'base_portal' => $validated['base_portal'],
            'badge_color' => $validated['badge_color'],
            'permissions' => $permissions,
            'is_system' => false,
        ]);

        Role::flushCache();

        AuditLogger::record(
            'role.create',
            'roles',
            "Superadmin menambahkan role baru '{$role->name}' ({$role->slug}) pada portal {$role->base_portal}.",
            $role,
            ['permissions' => $permissions, 'base_portal' => $role->base_portal]
        );

        return redirect()->route('superadmin.roles')->with('success', "Role '{$role->name}' ({$role->slug}) berhasil ditambahkan dan siap digunakan.");
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $catalogKeys = array_keys(Role::permissionCatalog());

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'base_portal' => ['nullable', Rule::in(array_keys(Role::portalOptions()))],
            'badge_color' => ['required', Rule::in(array_keys(Role::badgeOptions()))],
            'permissions' => 'nullable|array',
            'permissions.*' => ['string', Rule::in($catalogKeys)],
        ]);

        $basePortal = $role->is_system ? $role->base_portal : ($validated['base_portal'] ?? $role->base_portal);

        if ($role->slug === 'superadmin') {
            $permissions = ['*'];
        } elseif ($basePortal === 'superadmin') {
            $permissions = ['*'];
        } elseif ($basePortal === 'admin') {
            $selected = array_values(array_unique($validated['permissions'] ?? []));
            if (empty($selected)) {
                return back()->withErrors([
                    'permissions' => 'Role pada Portal Manajemen wajib memiliki minimal satu hak akses modul.',
                ]);
            }
            $permissions = $selected;
        } else {
            $permissions = [];
        }

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'base_portal' => $basePortal,
            'badge_color' => $validated['badge_color'],
            'permissions' => $permissions,
        ]);

        Role::flushCache();

        AuditLogger::record(
            'role.update',
            'roles',
            "Superadmin memperbarui konfigurasi & hak akses role '{$role->name}' ({$role->slug}).",
            $role,
            ['permissions' => $permissions, 'base_portal' => $basePortal]
        );

        return redirect()->route('superadmin.roles')->with('success', "Konfigurasi role '{$role->name}' berhasil diperbarui.");
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);

        if ($role->is_system) {
            return back()->with('error', "Role bawaan sistem '{$role->name}' dilindungi dan tidak dapat dihapus.");
        }

        $assignedCount = User::where('role', $role->slug)->count();
        if ($assignedCount > 0) {
            return back()->with('error', "Role '{$role->name}' masih digunakan oleh {$assignedCount} akun pengguna. Ubah role pengguna terkait terlebih dahulu sebelum menghapus role ini.");
        }

        $name = $role->name;
        $slug = $role->slug;
        $role->delete();
        Role::flushCache();

        AuditLogger::record(
            'role.delete',
            'roles',
            "Superadmin menghapus role kustom '{$name}' ({$slug})."
        );

        return redirect()->route('superadmin.roles')->with('success', "Role '{$name}' berhasil dihapus dari sistem.");
    }

    public function sessionsAndQueues(Request $request)
    {
        $currentSessionId = $request->hasSession() ? $request->session()->getId() : '';

        $rawSessions = Schema::hasTable('sessions')
            ? DB::table('sessions')
                ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
                ->select(
                    'sessions.id',
                    'sessions.user_id',
                    'sessions.ip_address',
                    'sessions.user_agent',
                    'sessions.last_activity',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role'
                )
                ->orderByDesc('sessions.last_activity')
                ->limit(100)
                ->get()
            : collect();

        $sessions = $rawSessions->map(function ($row) use ($currentSessionId) {
            $roleDef = $row->user_role ? Role::findBySlug($row->user_role) : null;

            return [
                'id' => $row->id,
                'user_id' => $row->user_id,
                'user_name' => $row->user_name ?? 'Tamu (Belum Login)',
                'user_email' => $row->user_email ?? '-',
                'role_label' => $roleDef?->name ?? ($row->user_role ? ucfirst((string) $row->user_role) : 'Publik'),
                'role_badge' => $roleDef?->badgeClasses() ?? 'bg-slate-100 text-slate-600',
                'ip_address' => $row->ip_address ?: '-',
                'device' => $this->summarizeUserAgent($row->user_agent),
                'user_agent' => $row->user_agent ?: '-',
                'last_active' => Carbon::createFromTimestamp((int) $row->last_activity)->diffForHumans(),
                'last_active_exact' => Carbon::createFromTimestamp((int) $row->last_activity)->format('d/m/Y H:i:s'),
                'is_current' => $row->id === $currentSessionId,
            ];
        });

        $pendingJobsCount = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
        $pendingJobs = Schema::hasTable('jobs')
            ? DB::table('jobs')->orderBy('id')->limit(30)->get()->map(function ($job) {
                $payload = json_decode((string) $job->payload, true) ?: [];

                return [
                    'id' => $job->id,
                    'queue' => $job->queue,
                    'name' => $payload['displayName'] ?? 'QueuedJob',
                    'attempts' => $job->attempts,
                    'available_at' => Carbon::createFromTimestamp((int) $job->available_at)->format('d/m/Y H:i:s'),
                ];
            })
            : collect();

        $failedJobsCount = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $failedJobs = Schema::hasTable('failed_jobs')
            ? DB::table('failed_jobs')->orderByDesc('failed_at')->limit(50)->get()->map(function ($job) {
                $payload = json_decode((string) $job->payload, true) ?: [];
                $exceptionLines = preg_split('/\r\n|\r|\n/', trim((string) $job->exception));

                return [
                    'uuid' => $job->uuid,
                    'queue' => $job->queue,
                    'connection' => $job->connection,
                    'name' => $payload['displayName'] ?? 'FailedJob',
                    'failed_at' => $job->failed_at,
                    'summary' => Str::limit($exceptionLines[0] ?? 'Exception', 160),
                    'exception' => (string) $job->exception,
                ];
            })
            : collect();

        return view('superadmin.sessions', compact(
            'sessions',
            'pendingJobsCount',
            'pendingJobs',
            'failedJobsCount',
            'failedJobs'
        ));
    }

    public function revokeSession(Request $request, string $sessionId)
    {
        if (!Schema::hasTable('sessions')) {
            return back()->with('error', 'Tabel sessions tidak tersedia pada driver sesi saat ini.');
        }

        if ($request->hasSession() && $sessionId === $request->session()->getId()) {
            return back()->with('error', 'Anda tidak dapat memutus sesi aktif yang sedang Anda gunakan.');
        }

        $row = DB::table('sessions')->where('id', $sessionId)->first();
        if (!$row) {
            return back()->with('error', 'Sesi tidak ditemukan atau sudah berakhir.');
        }

        $targetUser = $row->user_id ? User::find($row->user_id) : null;
        DB::table('sessions')->where('id', $sessionId)->delete();

        AuditLogger::record(
            'session.revoke',
            'security',
            'Superadmin memutus paksa (force logout) sesi perangkat milik ' . ($targetUser?->name ?? 'Tamu') . " (IP: {$row->ip_address}).",
            $targetUser
        );

        return back()->with('success', 'Sesi perangkat berhasil diputus secara paksa.');
    }

    public function revokeOtherSessions(Request $request)
    {
        if (!Schema::hasTable('sessions')) {
            return back()->with('error', 'Tabel sessions tidak tersedia pada driver sesi saat ini.');
        }

        $currentId = $request->hasSession() ? $request->session()->getId() : '';
        $deleted = DB::table('sessions')->where('id', '!=', $currentId)->delete();

        AuditLogger::record(
            'session.revoke_others',
            'security',
            "Superadmin memutus seluruh sesi login pengguna lain ({$deleted} sesi dihentikan)."
        );

        return back()->with('success', "Berhasil memutus {$deleted} sesi aktif pengguna lain.");
    }

    public function retryFailedJob(string $uuid)
    {
        try {
            Artisan::call('queue:retry', ['id' => [$uuid]]);
            AuditLogger::record('queue.retry', 'system', "Superadmin menjalankan ulang failed job UUID: {$uuid}.");

            return back()->with('success', "Failed job ({$uuid}) berhasil dimasukkan kembali ke antrean eksekusi.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengulang job: ' . $e->getMessage());
        }
    }

    public function retryAllFailedJobs()
    {
        try {
            Artisan::call('queue:retry', ['id' => ['all']]);
            AuditLogger::record('queue.retry_all', 'system', 'Superadmin menjalankan ulang seluruh failed jobs.');

            return back()->with('success', 'Seluruh failed jobs berhasil dikembalikan ke antrean eksekusi.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengulang seluruh job: ' . $e->getMessage());
        }
    }

    public function forgetFailedJob(string $uuid)
    {
        try {
            Artisan::call('queue:forget', ['id' => $uuid]);
            AuditLogger::record('queue.forget', 'system', "Superadmin menghapus failed job UUID: {$uuid}.");

            return back()->with('success', "Catatan failed job ({$uuid}) berhasil dihapus.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus failed job: ' . $e->getMessage());
        }
    }

    public function flushFailedJobs()
    {
        try {
            Artisan::call('queue:flush');
            AuditLogger::record('queue.flush', 'system', 'Superadmin mengosongkan seluruh daftar failed_jobs.');

            return back()->with('success', 'Seluruh daftar failed jobs berhasil dibersihkan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membersihkan daftar failed jobs: ' . $e->getMessage());
        }
    }

    public function logs(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $fileExists = File::exists($logFile);
        $fileSize = $fileExists ? File::size($logFile) : 0;

        $entries = $fileExists ? $this->parseLaravelLog($logFile, 250) : [];

        $levelCounts = [
            'ALL' => count($entries),
            'ERROR' => 0,
            'WARNING' => 0,
            'INFO' => 0,
        ];

        foreach ($entries as $entry) {
            if (in_array($entry['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'], true)) {
                $levelCounts['ERROR']++;
            } elseif (in_array($entry['level'], ['WARNING', 'NOTICE'], true)) {
                $levelCounts['WARNING']++;
            } else {
                $levelCounts['INFO']++;
            }
        }

        $selectedLevel = strtoupper((string) $request->get('level', 'ALL'));
        $search = trim((string) $request->get('search', ''));

        if ($selectedLevel !== 'ALL') {
            $entries = array_values(array_filter($entries, function ($item) use ($selectedLevel) {
                if ($selectedLevel === 'ERROR') {
                    return in_array($item['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'], true);
                }
                if ($selectedLevel === 'WARNING') {
                    return in_array($item['level'], ['WARNING', 'NOTICE'], true);
                }

                return !in_array($item['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY', 'WARNING', 'NOTICE'], true);
            }));
        }

        if ($search !== '') {
            $entries = array_values(array_filter($entries, function ($item) use ($search) {
                return stripos($item['message'], $search) !== false || stripos($item['stack'], $search) !== false;
            }));
        }

        $formattedSize = $this->formatBytes($fileSize);

        return view('superadmin.logs', compact(
            'entries',
            'fileExists',
            'fileSize',
            'formattedSize',
            'levelCounts',
            'selectedLevel',
            'search'
        ));
    }

    public function downloadLog()
    {
        $logFile = storage_path('logs/laravel.log');
        if (!File::exists($logFile)) {
            return back()->with('error', 'Berkas laravel.log belum tersedia.');
        }

        AuditLogger::record('logs.download', 'system', 'Superadmin mengunduh berkas laravel.log.');

        return response()->download($logFile, 'laravel-' . now()->format('Ymd-His') . '.log');
    }

    public function clearLog()
    {
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        AuditLogger::record('logs.clear', 'system', 'Superadmin mengosongkan isi berkas laravel.log.');

        return redirect()->route('superadmin.logs')->with('success', 'Berkas log aplikasi berhasil dikosongkan.');
    }

    public function auditLogs(Request $request)
    {
        $query = AuditLog::with(['user', 'impersonator']);

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest()->paginate(20)->withQueryString();
        $modules = AuditLog::select('module')->distinct()->orderBy('module')->pluck('module');

        return view('superadmin.audit-logs', compact('logs', 'modules'));
    }

    public function settings(AttendanceService $attendanceService)
    {
        $settings = SystemSetting::allCached();
        $dailyQrToken = $attendanceService->getDailyQrToken();
        $waConfig = [
            'driver' => config('services.whatsapp.driver', env('WA_DRIVER', 'log')),
            'url' => config('services.whatsapp.url', env('WA_API_URL', 'https://api.fonnte.com/send')),
            'has_token' => !empty(config('services.whatsapp.token', env('WA_API_TOKEN', ''))),
        ];
        $mailConfig = [
            'mailer' => config('mail.default', 'log'),
            'host' => config('mail.mailers.smtp.host', '-'),
            'port' => config('mail.mailers.smtp.port', '-'),
            'from' => config('mail.from.address', 'hello@example.com'),
        ];

        return view('superadmin.settings', compact('settings', 'dailyQrToken', 'waConfig', 'mailConfig'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'office_lat' => 'required|numeric|between:-90,90',
            'office_lng' => 'required|numeric|between:-180,180',
            'max_geofence_meters' => 'required|integer|min:20|max:50000',
            'late_cutoff_time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'allowance_wfo_rate' => 'required|integer|min:0|max:5000000',
            'allowance_wfh_rate' => 'required|integer|min:0|max:5000000',
            'allowance_wfa_rate' => 'required|integer|min:0|max:5000000',
        ]);

        SystemSetting::setMany([
            'office_lat' => ['value' => $validated['office_lat'], 'type' => 'float'],
            'office_lng' => ['value' => $validated['office_lng'], 'type' => 'float'],
            'max_geofence_meters' => ['value' => $validated['max_geofence_meters'], 'type' => 'integer'],
            'late_cutoff_time' => ['value' => $validated['late_cutoff_time'], 'type' => 'string'],
            'allowance_wfo_rate' => ['value' => $validated['allowance_wfo_rate'], 'type' => 'integer'],
            'allowance_wfh_rate' => ['value' => $validated['allowance_wfh_rate'], 'type' => 'integer'],
            'allowance_wfa_rate' => ['value' => $validated['allowance_wfa_rate'], 'type' => 'integer'],
        ], Auth::id());

        AuditLogger::record(
            'settings.update',
            'system',
            "Superadmin memperbarui parameter operasional (Radius GPS: {$validated['max_geofence_meters']}m, Batas Masuk: {$validated['late_cutoff_time']}, Tarif WFO: Rp " . number_format((int) $validated['allowance_wfo_rate'], 0, ',', '.') . ').',
            null,
            $validated
        );

        return redirect()->route('superadmin.settings')->with('success', 'Parameter operasional sistem berhasil disimpan dan langsung berlaku.');
    }

    public function testWhatsAppGateway(Request $request, WhatsAppService $waService)
    {
        $validated = $request->validate([
            'target_phone' => 'required|string|min:8|max:25',
            'test_message' => 'nullable|string|max:500',
        ]);

        $msg = trim((string) ($validated['test_message'] ?? ''));
        if ($msg === '') {
            $msg = '[UJI DIAGNOSTIK PELINDO] Koneksi Gateway WhatsApp dari panel Superadmin berjalan normal pada ' . now()->format('d/m/Y H:i:s') . ' WIB.';
        }

        $ok = $waService->sendMessage($validated['target_phone'], $msg);
        $driver = config('services.whatsapp.driver', env('WA_DRIVER', 'log'));

        AuditLogger::record(
            'system.test_wa',
            'system',
            "Superadmin menguji pengiriman WhatsApp ke {$validated['target_phone']} (driver: {$driver}, hasil: " . ($ok ? 'berhasil' : 'gagal') . ').'
        );

        if (!$ok) {
            return back()->with('error', "Pengiriman uji WhatsApp ke {$validated['target_phone']} gagal. Periksa WA_API_TOKEN, URL endpoint, atau Log Error Laravel.");
        }

        $note = $driver === 'log'
            ? " (Mode simulasi 'log' aktif: pesan dicatat ke storage/logs/laravel.log)."
            : " melalui gateway {$driver}.";

        return back()->with('success', "Pesan uji WhatsApp ke {$validated['target_phone']} berhasil diproses{$note}");
    }

    public function testEmailGateway(Request $request)
    {
        $validated = $request->validate([
            'target_email' => 'required|email|max:150',
        ]);

        $mailer = config('mail.default', 'log');
        $subject = '[Diagnostik Pelindo Internship] Uji Koneksi Email Server';
        $body = "Halo,\n\nEmail ini dikirim secara otomatis dari Panel Superadmin Pelindo Internship Portal pada "
            . now()->format('d/m/Y H:i:s') . " WIB untuk memverifikasi kesiapan layanan pengiriman email ({$mailer}).\n\nSalam,\nTim Pengembang IT Pelindo";

        try {
            Mail::raw($body, function ($message) use ($validated, $subject) {
                $message->to($validated['target_email'])->subject($subject);
            });

            AuditLogger::record(
                'system.test_email',
                'system',
                "Superadmin menguji pengiriman Email SMTP ke {$validated['target_email']} (mailer: {$mailer})."
            );

            $note = $mailer === 'log'
                ? " (Mailer aktif adalah 'log': isi surat tercatat di storage/logs/laravel.log)."
                : " menggunakan mailer '{$mailer}'.";

            return back()->with('success', "Email uji coba berhasil dikirim ke {$validated['target_email']}{$note}");
        } catch (\Throwable $e) {
            AuditLogger::record(
                'system.test_email_failed',
                'system',
                "Uji pengiriman email ke {$validated['target_email']} gagal: " . $e->getMessage()
            );

            return back()->with('error', 'Pengiriman email gagal: ' . $e->getMessage());
        }
    }

    public function triggerReminders()
    {
        try {
            Artisan::call('internship:send-reminders', ['--force' => true]);
            $output = trim(Artisan::output());

            AuditLogger::record(
                'system.run_reminders',
                'system',
                'Superadmin memicu pengiriman pengingat harian (internship:send-reminders) secara manual.'
            );

            return back()->with('success', 'Scheduler pengingat berhasil dijalankan. ' . ($output !== '' ? $output : ''));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menjalankan scheduler pengingat: ' . $e->getMessage());
        }
    }

    public function downloadDatabaseBackup()
    {
        $driver = config('database.default');
        $timestamp = now()->format('Ymd_His');

        AuditLogger::record(
            'system.backup_db',
            'system',
            "Superadmin mengunduh cadangan (backup) database ({$driver})."
        );

        if ($driver === 'sqlite') {
            $sqlitePath = config('database.connections.sqlite.database');
            if ($sqlitePath && $sqlitePath !== ':memory:' && file_exists($sqlitePath)) {
                return response()->download($sqlitePath, "pelindo_backup_{$timestamp}.sqlite");
            }
        }

        // Portable JSON backup fallback for MySQL/MariaDB or in-memory SQLite
        $tablesToExport = [
            'roles',
            'divisions',
            'users',
            'supervisor_profiles',
            'intern_profiles',
            'daily_activities',
            'attendances',
            'leave_requests',
            'allowance_periods',
            'allowance_claims',
            'intern_assessments',
            'certificates',
            'intern_surveys',
            'announcements',
            'system_settings',
            'audit_logs',
        ];

        $snapshot = [
            'exported_at' => now()->toIso8601String(),
            'driver' => $driver,
            'tables' => [],
        ];

        foreach ($tablesToExport as $tbl) {
            if (Schema::hasTable($tbl)) {
                $snapshot['tables'][$tbl] = DB::table($tbl)->get()->toArray();
            }
        }

        $fileName = "pelindo_backup_{$timestamp}.json";

        return response()->streamDownload(function () use ($snapshot) {
            echo json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $fileName, [
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
    }

    private function collectSystemHealth(): array
    {
        $dbConnected = false;
        $dbLatencyMs = null;
        $dbDriver = config('database.default');
        $dbSizeBytes = 0;

        try {
            $t0 = microtime(true);
            DB::connection()->getPdo();
            $dbLatencyMs = round((microtime(true) - $t0) * 1000, 2);
            $dbConnected = true;

            if ($dbDriver === 'sqlite') {
                $sqlitePath = config('database.connections.sqlite.database');
                if ($sqlitePath && file_exists($sqlitePath)) {
                    $dbSizeBytes = (int) filesize($sqlitePath);
                }
            } elseif (in_array($dbDriver, ['mysql', 'mariadb'], true)) {
                $dbName = config('database.connections.' . $dbDriver . '.database');
                $row = DB::selectOne(
                    'SELECT SUM(data_length + index_length) AS total_bytes FROM information_schema.TABLES WHERE table_schema = ?',
                    [$dbName]
                );
                $dbSizeBytes = (int) ($row->total_bytes ?? 0);
            }
        } catch (\Throwable $e) {
            $dbConnected = false;
        }

        $publicStoragePath = storage_path('app/public');
        $uploadFileCount = 0;
        $uploadBytes = 0;

        if (is_dir($publicStoragePath)) {
            foreach (File::allFiles($publicStoragePath) as $file) {
                $uploadFileCount++;
                $uploadBytes += $file->getSize();
            }
        }

        $diskTotal = @disk_total_space(base_path()) ?: 0;
        $diskFree = @disk_free_space(base_path()) ?: 0;
        $diskUsed = max(0, $diskTotal - $diskFree);
        $diskUsedPct = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

        $logPath = storage_path('logs/laravel.log');
        $logBytes = File::exists($logPath) ? File::size($logPath) : 0;
        $recentLogEntries = File::exists($logPath) ? $this->parseLaravelLog($logPath, 100) : [];
        $errorLogCount = count(array_filter($recentLogEntries, fn ($e) => in_array($e['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'], true)));

        $pendingJobs = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
        $failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $activeSessions = Schema::hasTable('sessions') ? DB::table('sessions')->count() : 0;

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => config('app.env'),
            'debug_mode' => (bool) config('app.debug'),
            'timezone' => config('app.timezone'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
            'storage_linked' => file_exists(public_path('storage')),
            'db_connected' => $dbConnected,
            'db_driver' => strtoupper((string) $dbDriver),
            'db_latency_ms' => $dbLatencyMs,
            'db_size_human' => $this->formatBytes($dbSizeBytes),
            'upload_files_count' => $uploadFileCount,
            'upload_size_human' => $this->formatBytes($uploadBytes),
            'disk_total_human' => $this->formatBytes((int) $diskTotal),
            'disk_free_human' => $this->formatBytes((int) $diskFree),
            'disk_used_human' => $this->formatBytes((int) $diskUsed),
            'disk_used_pct' => $diskUsedPct,
            'log_size_human' => $this->formatBytes($logBytes),
            'error_log_count' => $errorLogCount,
            'pending_jobs' => $pendingJobs,
            'failed_jobs' => $failedJobs,
            'active_sessions' => $activeSessions,
        ];
    }

    private function summarizeUserAgent(?string $ua): string
    {
        if (!$ua) {
            return 'Perangkat Tidak Dikenal';
        }

        $browser = match (true) {
            stripos($ua, 'Edg/') !== false => 'Edge',
            stripos($ua, 'Chrome/') !== false => 'Chrome',
            stripos($ua, 'Firefox/') !== false => 'Firefox',
            stripos($ua, 'Safari/') !== false && stripos($ua, 'Chrome/') === false => 'Safari',
            stripos($ua, 'curl') !== false => 'cURL / CLI',
            default => 'Browser Web',
        };

        $os = match (true) {
            stripos($ua, 'Windows') !== false => 'Windows',
            stripos($ua, 'Android') !== false => 'Android',
            stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false => 'iOS',
            stripos($ua, 'Mac OS') !== false => 'macOS',
            stripos($ua, 'Linux') !== false => 'Linux',
            default => 'OS Lainnya',
        };

        return "{$browser} ({$os})";
    }

    private function parseLaravelLog(string $path, int $limit = 200): array
    {
        $maxBytes = 512 * 1024; // Read last 512 KB safely
        $size = File::size($path);
        if ($size === 0) {
            return [];
        }

        $fp = fopen($path, 'rb');
        if (!$fp) {
            return [];
        }

        if ($size > $maxBytes) {
            fseek($fp, -$maxBytes, SEEK_END);
            fgets($fp); // Discard partial line
        }

        $raw = stream_get_contents($fp) ?: '';
        fclose($fp);

        $pattern = '/^\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}[^\]]*)\]\s+([a-zA-Z0-9_-]+)\.([A-Z]+):\s*(.*)/m';
        preg_match_all($pattern, $raw, $matches, PREG_OFFSET_CAPTURE);

        if (empty($matches[0])) {
            return [];
        }

        $entries = [];
        $totalMatches = count($matches[0]);

        for ($i = 0; $i < $totalMatches; $i++) {
            $startOffset = $matches[0][$i][1];
            $headerLen = strlen($matches[0][$i][0]);
            $nextOffset = ($i + 1 < $totalMatches) ? $matches[0][$i + 1][1] : strlen($raw);

            $bodyAfterHeader = trim(substr($raw, $startOffset + $headerLen, $nextOffset - ($startOffset + $headerLen)));

            $entries[] = [
                'timestamp' => $matches[1][$i][0],
                'env' => $matches[2][$i][0],
                'level' => strtoupper($matches[3][$i][0]),
                'message' => trim($matches[4][$i][0]),
                'stack' => $bodyAfterHeader,
            ];
        }

        $entries = array_reverse($entries);

        return array_slice($entries, 0, $limit);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return round($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow];
    }
}
