<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\DailyActivity;
use App\Models\Division;
use App\Models\InternProfile;
use App\Models\InternSurvey;
use App\Models\InternTask;
use App\Models\LeaveRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ComprehensiveDevelopmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::ensureDefaults();
    }

    public function test_supervisor_and_admin_intern_search_filters_do_not_leak_data(): void
    {
        $divA = Division::create(['name' => 'Divisi A', 'code' => 'DIVA', 'quota' => 10]);
        $divB = Division::create(['name' => 'Divisi B', 'code' => 'DIVB', 'quota' => 10]);

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $spv1 = User::factory()->create(['role' => 'supervisor', 'is_active' => true]);
        $spv2 = User::factory()->create(['role' => 'supervisor', 'is_active' => true]);

        $intern1 = User::factory()->create(['name' => 'Budi Santoso', 'role' => 'intern', 'is_active' => true]);
        $intern2 = User::factory()->create(['name' => 'Ani Rahma', 'role' => 'intern', 'is_active' => true]);

        InternProfile::create([
            'user_id' => $intern1->id,
            'division_id' => $divA->id,
            'supervisor_id' => $spv1->id,
            'student_id_number' => 'NIM-1001',
            'institution' => 'ITS',
            'major' => 'TI',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        InternProfile::create([
            'user_id' => $intern2->id,
            'division_id' => $divB->id,
            'supervisor_id' => $spv2->id,
            'student_id_number' => 'NIM-2002',
            'institution' => 'ITS',
            'major' => 'SI',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        // Supervisor 1 searches "ITS" - must only see intern1, not intern2
        $resSpv = $this->actingAs($spv1)->get(route('supervisor.interns', ['search' => 'ITS']));
        $resSpv->assertOk();
        $resSpv->assertSee('Budi Santoso');
        $resSpv->assertDontSee('Ani Rahma');

        // Admin searches "ITS" filtered to Divisi A - must only see intern1
        $resAdmin = $this->actingAs($admin)->get(route('admin.interns.index', [
            'search' => 'ITS',
            'division_id' => $divA->id,
        ]));
        $resAdmin->assertOk();
        $resAdmin->assertSee('Budi Santoso');
        $resAdmin->assertDontSee('Ani Rahma');
    }

    public function test_mentor_portal_tasks_notes_and_intern_task_submission(): void
    {
        $div = Division::create(['name' => 'Divisi TI', 'code' => 'TI', 'quota' => 5]);
        $spv = User::factory()->create(['role' => 'supervisor', 'is_active' => true]);
        $mentor = User::factory()->create(['role' => 'mentor', 'is_active' => true]);
        $intern = User::factory()->create(['role' => 'intern', 'is_active' => true]);

        InternProfile::create([
            'user_id' => $intern->id,
            'division_id' => $div->id,
            'supervisor_id' => $spv->id,
            'mentor_id' => $mentor->id,
            'student_id_number' => '12345',
            'institution' => 'UNAIR',
            'major' => 'SI',
            'start_date' => now()->subWeek(),
            'end_date' => now()->addMonths(2),
            'status' => 'active',
        ]);

        $this->actingAs($mentor)->get(route('mentor.dashboard'))->assertOk();

        // Mentor assigns a task
        $this->actingAs($mentor)->post(route('mentor.tasks.store'), [
            'intern_id' => $intern->id,
            'title' => 'Buat Dokumentasi API',
            'description' => 'Swagger lengkap',
        ])->assertRedirect();

        $task = InternTask::firstOrFail();

        // Intern submits the task
        $this->actingAs($intern)->post(route('intern.tasks.submit', $task->id), [
            'status' => 'submitted',
            'submission_note' => 'Sudah selesai dikerjakan.',
        ])->assertRedirect();

        $this->assertEquals('submitted', $task->fresh()->status);

        // Mentor approves task and records mentoring note
        $this->actingAs($mentor)->post(route('mentor.tasks.review', $task->id), [
            'status' => 'approved',
            'reviewer_note' => 'Bagus!',
        ])->assertRedirect();

        $this->assertEquals('approved', $task->fresh()->status);

        $this->actingAs($mentor)->post(route('mentor.notes.store'), [
            'intern_id' => $intern->id,
            'session_date' => now()->toDateString(),
            'topic' => 'Review Sprint 1',
            'notes' => 'Progres sangat baik.',
        ])->assertRedirect();

        $this->assertDatabaseHas('mentoring_notes', [
            'intern_id' => $intern->id,
            'mentor_id' => $mentor->id,
            'topic' => 'Review Sprint 1',
        ]);
    }

    public function test_attendance_correction_leave_cancel_override_and_bulk_approve(): void
    {
        $div = Division::create(['name' => 'Operasional', 'code' => 'OPS', 'quota' => 5]);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $spv = User::factory()->create(['role' => 'supervisor', 'is_active' => true]);
        $spvDelegate = User::factory()->create(['role' => 'supervisor', 'is_active' => true]);
        $intern = User::factory()->create(['role' => 'intern', 'is_active' => true]);

        InternProfile::create([
            'user_id' => $intern->id,
            'division_id' => $div->id,
            'supervisor_id' => $spv->id,
            'student_id_number' => '998877',
            'institution' => 'PPNS',
            'major' => 'Teknik',
            'start_date' => now()->subWeek(),
            'end_date' => now()->addMonths(2),
            'status' => 'active',
        ]);

        // 1. Intern requests attendance correction
        $this->actingAs($intern)->post(route('intern.attendance.correction'), [
            'date' => now()->subDay()->toDateString(),
            'correction_type' => 'missed_checkout',
            'requested_check_in' => '08:00',
            'requested_check_out' => '17:00',
            'work_location' => 'WFO',
            'reason' => 'Lupa menekan tombol check-out saat pulang kantor.',
        ])->assertRedirect();

        $correction = AttendanceCorrection::firstOrFail();

        // Supervisor delegates to spvDelegate
        $this->actingAs($spv)->post(route('supervisor.delegation.update'), [
            'delegate_supervisor_id' => $spvDelegate->id,
            'delegation_start_date' => now()->subDay()->toDateString(),
            'delegation_end_date' => now()->addDay()->toDateString(),
        ])->assertRedirect();

        // Delegated supervisor approves the correction
        $this->actingAs($spvDelegate)->post(route('supervisor.attendance.corrections.approve', $correction->id))
            ->assertRedirect();

        $this->assertEquals('approved', $correction->fresh()->status);
        $this->assertDatabaseHas('attendances', [
            'intern_id' => $intern->id,
            'check_out_time' => '17:00:00',
        ]);

        // 2. Intern creates and cancels pending leave, then creates another overridden by Admin
        $this->actingAs($intern)->post(route('intern.leaves.store'), [
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'type' => 'izin',
            'reason' => 'Keperluan keluarga yang mendadak sekali.',
        ])->assertRedirect();

        $leave1 = LeaveRequest::firstOrFail();
        $this->actingAs($intern)->delete(route('intern.leaves.cancel', $leave1->id))->assertRedirect();
        $this->assertDatabaseMissing('leave_requests', ['id' => $leave1->id]);

        $this->actingAs($intern)->post(route('intern.leaves.store'), [
            'start_date' => now()->addDays(4)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'type' => 'sakit',
            'reason' => 'Demam tinggi dan perlu istirahat total.',
        ])->assertRedirect();

        $leave2 = LeaveRequest::firstOrFail();
        $this->actingAs($admin)->post(route('admin.leaves.approve', $leave2->id))->assertRedirect();
        $this->assertEquals('approved', $leave2->fresh()->status);

        // 3. Draft delete & bulk approve logbooks
        $draft = DailyActivity::create([
            'intern_id' => $intern->id,
            'supervisor_id' => $spv->id,
            'date' => now()->subDays(3)->toDateString(),
            'work_location' => 'WFO',
            'activity_description' => 'Draft sementara',
            'status' => 'draft',
        ]);
        $this->actingAs($intern)->delete(route('intern.activities.destroy', $draft->id))->assertRedirect();
        $this->assertDatabaseMissing('daily_activities', ['id' => $draft->id]);

        $pending1 = DailyActivity::create([
            'intern_id' => $intern->id,
            'supervisor_id' => $spv->id,
            'date' => now()->subDays(2)->toDateString(),
            'work_location' => 'WFO',
            'activity_description' => 'Aktivitas harian 1',
            'status' => 'pending',
        ]);
        $this->actingAs($spv)->post(route('supervisor.activities.bulk_approve'), [
            'activity_ids' => [$pending1->id],
        ])->assertRedirect();
        $this->assertEquals('approved', $pending1->fresh()->status);
    }

    public function test_admin_csv_import_and_exports(): void
    {
        $div = Division::create(['name' => 'Divisi IT', 'code' => 'IT', 'quota' => 10]);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $spv = User::factory()->create(['email' => 'spv.it@pelindo.co.id', 'role' => 'supervisor', 'is_active' => true]);

        $csvContent = "name,email,student_id_number,institution,major,division_name,supervisor_email,mentor_email,start_date,end_date,password\n"
            . "Rina Putri,rina.putri@pelindo.co.id,2026001,ITS,Informatika,Divisi IT,spv.it@pelindo.co.id,,2026-11-01,2027-02-01,Secret123!\n";

        $file = UploadedFile::fake()->createWithContent('interns.csv', $csvContent);

        $this->actingAs($admin)->post(route('admin.interns.import'), [
            'csv_file' => $file,
        ])->assertRedirect(route('admin.interns.index'));

        $this->assertDatabaseHas('users', ['email' => 'rina.putri@pelindo.co.id', 'role' => 'intern']);

        $this->actingAs($admin)->get(route('admin.attendance.export_monthly', ['month' => now()->format('Y-m')]))->assertOk();
        $this->actingAs($admin)->get(route('admin.surveys.export'))->assertOk();
    }
}
