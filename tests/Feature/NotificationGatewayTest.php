<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Notification;
use App\Services\WhatsAppService;
use App\Mail\InternshipReminderMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotificationGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_whatsapp_service_phone_formatting(): void
    {
        $waService = new WhatsAppService();

        $this->assertEquals('628123456789', $waService->formatPhoneNumber('08123456789'));
        $this->assertEquals('628123456789', $waService->formatPhoneNumber('+62 812-3456-789'));
    }

    public function test_notification_send_to_user_dispatches_database_and_mail(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'Budi Intern',
            'email' => 'budi@pelindo.test',
            'phone' => '081299998888',
            'role' => 'intern',
        ]);

        $notification = Notification::sendToUser(
            $user,
            'Test Title',
            'Test Message Body'
        );

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'title' => 'Test Title',
            'message' => 'Test Message Body',
        ]);

        Mail::assertQueued(InternshipReminderMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->userName === 'Budi Intern';
        });
    }

    public function test_send_internship_reminders_command_executes_successfully(): void
    {
        Mail::fake();

        $this->artisan('internship:send-reminders --force')
            ->assertExitCode(0);
    }
}
