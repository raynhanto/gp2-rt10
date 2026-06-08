<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\IuranBayar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IuranStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public IuranBayar $bayar) {}

    public function envelope(): Envelope
    {
        $status = $this->bayar->status === 'verified' ? 'Dikonfirmasi' : 'Ditolak';
        return new Envelope(subject: "Pembayaran Iuran {$status} — RT 10 Golden Park 2");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.iuran-status');
    }
}
