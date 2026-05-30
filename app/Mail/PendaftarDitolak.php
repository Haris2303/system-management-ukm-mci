<?php

namespace App\Mail;

use App\Models\Pendaftar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftarDitolak extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Pendaftar $pendaftar,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Informasi Hasil Seleksi — UKM MCI',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pendaftar-ditolak',
            with: [
                'nama'   => $this->pendaftar->nama,
                'divisi' => $this->pendaftar->divisi?->nama ?? '-',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
