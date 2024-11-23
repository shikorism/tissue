<?php

namespace App\Mail;

use App\Moderation;
use App\ModerationAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Moderated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Moderation $moderation)
    {
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->makeSubject(),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.moderated',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }

    private function makeSubject()
    {
        switch ($this->moderation->action) {
            case ModerationAction::SuspendCheckin:
                return '【警告】チェックインの非公開措置について | ' . config('app.name');
            case ModerationAction::SuspendUser:
                return '【警告】アカウントの非公開措置について | ' . config('app.name');
            default:
                throw new \LogicException('unknown action');
        }
    }
}
