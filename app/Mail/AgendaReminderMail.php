<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Agenda;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgendaReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Agenda $agenda) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Agenda RT: ' . $this->agenda->judul);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.agenda-reminder');
    }
}
