<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Pengumuman;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PengumumanMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Pengumuman $pengumuman) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[RT 10] ' . $this->pengumuman->judul);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.pengumuman');
    }
}
