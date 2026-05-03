<?php

namespace App\Mail;

use App\Models\Rechnung;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MahnungMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Rechnung $rechnung) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Payment reminder — invoice #:id', ['id' => $this->rechnung->id]),
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'mail.mahnung',
        );
    }
}
