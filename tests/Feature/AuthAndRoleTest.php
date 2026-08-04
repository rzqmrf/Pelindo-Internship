<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/intern/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_intern_can_access_intern_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'intern',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/intern/dashboard');
        $response->assertStatus(200);
    }

    public function test_supervisor_can_access_supervisor_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/supervisor/dashboard');
        $response->assertStatus(200);
    }
}
