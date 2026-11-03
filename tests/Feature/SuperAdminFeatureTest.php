<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AllowanceService;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role) . ' Tester',
            'email' => $email,
            'role' => $role,
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
    }

    public function test_superadmin_can_login_and_access_all_superadmin_pages(): void
    {
        $superAdmin = $this->makeUser('superadmin', 'superadmin@pelindo.co.id');

        $loginResp = $this->post('/login', [
            'email' => 'superadmin@pelindo.co.id',
            'password' => 'password123',
        ]);

        $loginResp->assertRedirect(route('superadmin.dashboard'));

        $this->actingAs($superAdmin)->get(route('superadmin.dashboard'))->assertOk()->assertSee('Pusat Kendali Pengembang Sistem');
        $this->actingAs($superAdmin)->get(route('superadmin.maintenance'))->assertOk()->assertSee('Kontrol Pemeliharaan');
        $this->actingAs($superAdmin)->get(route('superadmin.settings'))->assertOk()->assertSee('Parameter Operasional');
        $this->actingAs($superAdmin)->get(route('superadmin.users'))->assertOk()->assertSee('Manajemen Akun');
        $this->actingAs($superAdmin)->get(route('superadmin.roles'))->assertOk()->assertSee('Manajemen Role');
        $this->actingAs($superAdmin)->get(route('superadmin.sessions'))->assertOk()->assertSee('Sesi Perangkat Aktif');
        $this->actingAs($superAdmin)->get(route('superadmin.logs'))->assertOk()->assertSee('Laravel Error');
        $this->actingAs($superAdmin)->get(route('superadmin.audit_logs'))->assertOk()->assertSee('Audit Keamanan');
    }

    public function test_superadmin_can_create_custom_role_and_enforce_granular_permissions(): void
    {
        $superAdmin = $this->makeUser('superadmin', 'superadmin@pelindo.co.id');

        // 1. Create custom 'finance' role with only allowance.manage permission
        $this->actingAs($superAdmin)->post(route('superadmin.roles.store'), [
            'name' => 'Staff Finance',
            'slug' => 'finance',
            'description' => 'Khusus verifikasi klaim uang saku peserta magang.',
            'base_portal' => 'admin',
            'badge_color' => 'emerald',
            'permissions' => ['allowance.manage'],
        ])->assertRedirect(route('superadmin.roles'));

        $this->assertNotNull(Role::findBySlug('finance'));

        // 2. Create user assigned to custom 'finance' role via Superadmin Users UI
        $this->actingAs($superAdmin)->post(route('superadmin.users.store'), [
            'name' => 'Rina Finance',
            'email' => 'finance@pelindo.co.id',
            'role' => 'finance',
            'password' => 'password123',
        ])->assertRedirect(route('superadmin.users'));

        $financeUser = User::where('email', 'finance@pelindo.co.id')->firstOrFail();
        $this->assertSame('Staff Finance', $financeUser->roleLabel());
        $this->assertSame(route('admin.allowance.index'), $financeUser->homeRoute());

        // 3. Finance user can access /admin/allowance (200), but gets 403 on /admin/divisions & /admin/dashboard
        $this->actingAs($financeUser)->get(route('admin.allowance.index'))->assertOk();
        $this->actingAs($financeUser)->get(route('admin.divisions.index'))->assertForbidden();
        $this->actingAs($financeUser)->get(route('admin.dashboard'))->assertForbidden();

        // 4. Cannot delete role while assigned to active user, and cannot delete system role
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $this->actingAs($superAdmin)->delete(route('superadmin.roles.delete', $adminRole->id))
            ->assertSessionHas('error');

        $financeRole = Role::where('slug', 'finance')->firstOrFail();
        $this->actingAs($superAdmin)->delete(route('superadmin.roles.delete', $financeRole->id))
            ->assertSessionHas('error');

        $financeUser->delete();
        $this->actingAs($superAdmin)->delete(route('superadmin.roles.delete', $financeRole->id))
            ->assertRedirect(route('superadmin.roles'));
        $this->assertNull(Role::where('slug', 'finance')->first());
    }

    public function test_banner_maintenance_mode_and_secret_bypass_link(): void
    {
        $superAdmin = $this->makeUser('superadmin', 'superadmin@pelindo.co.id');
        $admin = $this->makeUser('admin', 'admin@pelindo.co.id');

        $this->actingAs($superAdmin)->post(route('superadmin.maintenance.update'), [
            'section' => 'maintenance',
            'maintenance_mode' => '1',
            'maintenance_allow_cert_verify' => '1',
            'maintenance_title' => 'Upgrade Database Pelindo',
            'maintenance_message' => 'Mohon tunggu hingga pemeliharaan selesai.',
            'maintenance_eta' => '2026-11-04T02:00',
            'maintenance_contact' => 'it.helpdesk@pelindo.co.id',
        ])->assertRedirect(route('superadmin.maintenance'));

        // Normal admin gets 503
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(503);

        // Generate secret bypass token and activate it on browser session
        $this->actingAs($superAdmin)->post(route('superadmin.maintenance.regenerate_bypass'));
        $bypassToken = (string) SystemSetting::getValue('maintenance_bypass_token');
        $this->assertNotEmpty($bypassToken);

        $this->actingAs($admin)->get(route('maintenance.bypass', ['token' => $bypassToken]))
            ->assertRedirect(route('admin.dashboard'));

        // Now admin with bypass session can access dashboard during maintenance!
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_dynamic_settings_gateway_diagnostics_and_backup(): void
    {
        $superAdmin = $this->makeUser('superadmin', 'superadmin@pelindo.co.id');

        $this->actingAs($superAdmin)->post(route('superadmin.settings.update'), [
            'office_lat' => '-7.2023000',
            'office_lng' => '112.7315000',
            'max_geofence_meters' => 450,
            'late_cutoff_time' => '08:40',
            'allowance_wfo_rate' => 80000,
            'allowance_wfh_rate' => 75000,
            'allowance_wfa_rate' => 75000,
        ])->assertRedirect(route('superadmin.settings'));

        $this->assertSame(450, app(AttendanceService::class)->getMaxAllowedDistance());
        $this->assertSame(80000, (new AllowanceService())->wfoRate);

        $this->actingAs($superAdmin)->post(route('superadmin.settings.test_wa'), [
            'target_phone' => '081234567890',
        ])->assertSessionHas('success');

        $this->actingAs($superAdmin)->post(route('superadmin.settings.test_email'), [
            'target_email' => 'qa@pelindo.co.id',
        ])->assertSessionHas('success');

        $this->actingAs($superAdmin)->get(route('superadmin.system.backup_db'))->assertOk();
        $this->assertTrue(AuditLog::where('action', 'system.backup_db')->exists());
    }

    public function test_impersonation_flow_and_report_idor_protection(): void
    {
        $superAdmin = $this->makeUser('superadmin', 'superadmin@pelindo.co.id');
        $intern1 = $this->makeUser('intern', 'intern1@pelindo.co.id');
        $intern2 = $this->makeUser('intern', 'intern2@pelindo.co.id');

        $this->actingAs($intern1)
            ->get(route('reports.pdf', ['intern_id' => $intern2->id]))
            ->assertForbidden();

        $resp = $this->actingAs($superAdmin)->post(route('superadmin.users.impersonate', $intern1->id));
        $resp->assertRedirect(route('intern.dashboard'));
        $this->assertAuthenticatedAs($intern1);

        $leaveResp = $this->post(route('impersonate.leave'));
        $leaveResp->assertRedirect(route('superadmin.users'));
        $this->assertAuthenticatedAs($superAdmin);
    }
}
