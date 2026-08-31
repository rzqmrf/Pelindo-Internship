<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\InternProfile;
use App\Models\InternAssessment;
use App\Models\Certificate;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class NewFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_intern_can_check_in_and_check_out(): void
    {
        $user = User::factory()->create([
            'role' => 'intern',
            'is_active' => true,
        ]);

        // Check in
        $response = $this->actingAs($user)->post('/intern/attendance/check-in', [
            'work_location' => 'WFO',
            'latitude' => -7.2023,
            'longitude' => 112.7315,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('attendances', [
            'intern_id' => $user->id,
            'work_location' => 'WFO',
        ]);
        $attendance = Attendance::where('intern_id', $user->id)->first();
        $this->assertNotNull($attendance);
        $this->assertEquals('WFO', $attendance->work_location);

        // Check out
        $responseOut = $this->actingAs($user)->post('/intern/attendance/check-out', [
            'latitude' => -7.2023,
            'longitude' => 112.7315,
        ]);

        $responseOut->assertSessionHas('success');
    }

    public function test_supervisor_can_submit_assessment(): void
    {
        $supervisor = User::factory()->create(['role' => 'supervisor', 'is_active' => true]);
        $intern = User::factory()->create(['role' => 'intern', 'is_active' => true]);
        InternProfile::create([
            'user_id' => $intern->id,
            'supervisor_id' => $supervisor->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($supervisor)->post("/supervisor/assessments/{$intern->id}", [
            'technical_skill' => 90,
            'discipline_score' => 85,
            'initiative_score' => 80,
            'teamwork_score' => 90,
            'attitude_score' => 95,
            'notes_strengths' => 'Sangat bertanggung jawab',
            'action' => 'submit',
        ]);

        $response->assertRedirect('/supervisor/assessments');
        $this->assertDatabaseHas('intern_assessments', [
            'intern_id' => $intern->id,
            'supervisor_id' => $supervisor->id,
            'status' => 'submitted',
            'predicate' => 'Sangat Baik (A)',
        ]);
    }

    public function test_admin_can_publish_certificate_and_public_can_verify(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $intern = User::factory()->create(['role' => 'intern', 'is_active' => true]);
        $supervisor = User::factory()->create(['role' => 'supervisor', 'is_active' => true]);

        $assessment = InternAssessment::create([
            'intern_id' => $intern->id,
            'supervisor_id' => $supervisor->id,
            'technical_skill' => 90,
            'discipline_score' => 90,
            'initiative_score' => 90,
            'teamwork_score' => 90,
            'attitude_score' => 90,
            'final_score' => 90,
            'predicate' => 'Sangat Baik (A)',
            'status' => 'submitted',
        ]);

        // Publish certificate
        $response = $this->actingAs($admin)->post("/admin/assessments/{$assessment->id}/publish");
        $response->assertSessionHas('success');

        $certificate = Certificate::where('intern_id', $intern->id)->first();
        $this->assertNotNull($certificate);

        // Public verification
        $publicResponse = $this->get("/verify-certificate/{$certificate->verification_token}");
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee($certificate->certificate_number);
    }
}
