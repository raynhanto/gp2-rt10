<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Donasi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonasiStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Donasi $donasi) {}

    public function envelope(): Envelope
    {
        $status = $this->donasi->status === 'verified' ? 'Diverifikasi' : 'Ditolak';
        return new Envelope(subject: "Donasi Anda {$status} — RT 10 Golden Park 2");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.donasi-status');
    }
}
