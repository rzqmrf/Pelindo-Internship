<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\InternAssessment;
use App\Models\Attendance;
use App\Models\AllowanceClaim;
use App\Http\Controllers\AttendanceController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewFeaturesPhase2Test extends TestCase
{
    use RefreshDatabase;

    public function test_intern_can_upload_final_project_report(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'intern',
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->create('laporan_akhir.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post('/intern/certificate/final-project', [
            'final_project_title' => 'Otomasi Sistem Pelindo PRO',
            'final_project_file' => $file,
            'final_project_link' => 'https://github.com/pelindo/pro',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('intern_assessments', [
            'intern_id' => $user->id,
            'final_project_title' => 'Otomasi Sistem Pelindo PRO',
            'final_project_link' => 'https://github.com/pelindo/pro',
        ]);
    }

    public function test_intern_can_check_in_with_valid_qr_token(): void
    {
        $user = User::factory()->create([
            'role' => 'intern',
            'is_active' => true,
        ]);

        $dailyToken = AttendanceController::getDailyQrToken();

        $response = $this->actingAs($user)->post('/intern/attendance/check-in', [
            'work_location' => 'WFO',
            'check_in_method' => 'qr_code',
            'qr_token' => $dailyToken,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'intern_id' => $user->id,
            'work_location' => 'WFO',
            'check_in_method' => 'qr_code',
            'qr_token_verified' => $dailyToken,
        ]);
    }

    public function test_hr_admin_can_export_payroll_excel_and_bulk_process(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $intern = User::factory()->create([
            'role' => 'intern',
            'is_active' => true,
        ]);

        $claim = AllowanceClaim::create([
            'intern_id' => $intern->id,
            'supervisor_id' => $admin->id,
            'period_number' => 1,
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-28',
            'wfo_count' => 15,
            'wfh_count' => 5,
            'leave_count' => 0,
            'total_allowance' => 1425000,
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '1400012345678',
            'bank_account_holder' => 'Budi Intern',
            'status' => 'approved_by_supervisor',
        ]);

        // Test Export Payroll
        $exportResponse = $this->actingAs($admin)->get('/admin/allowance/export-payroll');
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // Test Bulk Process
        $bulkResponse = $this->actingAs($admin)->post('/admin/allowance/bulk-process', [
            'claim_ids' => [$claim->id],
        ]);

        $bulkResponse->assertRedirect();
        $bulkResponse->assertSessionHas('success');

        $this->assertDatabaseHas('allowance_claims', [
            'id' => $claim->id,
            'status' => 'processed_by_hr',
        ]);
    }

    public function test_intern_can_checkin_with_wfa_location(): void
    {
        $user = User::factory()->create([
            'role' => 'intern',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/intern/attendance/check-in', [
            'work_location' => 'WFA',
            'check_in_method' => 'geofence',
            'latitude' => -7.250000,
            'longitude' => 112.750000,
            'address' => 'Tugas Luar di Pelabuhan Tanjung Perak',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'intern_id' => $user->id,
            'work_location' => 'WFA',
        ]);
    }
}
