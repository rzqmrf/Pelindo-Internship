<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnterpriseFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_pwa_manifest_is_accessible()
    {
        $response = $this->get('/manifest.json');
        $response->assertStatus(200)
            ->assertJsonPath('short_name', 'Pelindo PRO');
    }

    public function test_intern_can_submit_satisfaction_survey()
    {
        $intern = User::where('role', 'intern')->first();

        $response = $this->actingAs($intern)->get(route('intern.survey.index'));
        $response->assertStatus(200);

        $postResponse = $this->actingAs($intern)->post(route('intern.survey.store'), [
            'satisfaction_rating' => 5,
            'supervisor_rating' => 5,
            'facility_rating' => 4,
            'recommend_score' => 10,
            'feedback_notes' => 'Sangat berkesan dan menambah wawasan mengenai dunia kepelabuhanan.',
        ]);

        $postResponse->assertRedirect();
        $this->assertDatabaseHas('intern_surveys', [
            'intern_id' => $intern->id,
            'satisfaction_rating' => 5,
            'recommend_score' => 10,
        ]);
    }
}
