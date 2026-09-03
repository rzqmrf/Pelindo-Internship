<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InternshipReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $titleText;
    public string $messageText;

    /**
     * Create a new message instance.
     */
    public function __construct(string $userName, string $titleText, string $messageText)
    {
        $this->userName = $userName;
        $this->titleText = $titleText;
        $this->messageText = $messageText;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Pelindo PRO] ' . $this->titleText,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder_email',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
