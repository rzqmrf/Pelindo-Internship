<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Dispatch notification via Database, WhatsApp, and Email.
     */
    public static function sendToUser($user, string $title, string $message, bool $sendWhatsApp = true, bool $sendEmail = true): self
    {
        if (is_numeric($user)) {
            $user = User::find($user);
        }

        $notification = self::create([
            'user_id' => $user->id ?? $user,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);

        if ($user instanceof User) {
            // Send WhatsApp if phone exists and requested
            if ($sendWhatsApp && !empty($user->phone)) {
                try {
                    app(\App\Services\WhatsAppService::class)->sendMessage($user->phone, "*{$title}*\n\n{$message}");
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("Failed sending WA to user {$user->id}: " . $e->getMessage());
                }
            }

            // Send Email if email exists and requested
            if ($sendEmail && !empty($user->email)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->queue(
                        new \App\Mail\InternshipReminderMail($user->name, $title, $message)
                    );
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("Failed sending Email to user {$user->id}: " . $e->getMessage());
                }
            }
        }

        return $notification;
    }
}

