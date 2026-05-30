<?php

namespace App\Mail;

use App\Models\Pendaftar;
use App\Services\PendaftarService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftarLulus extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Pendaftar $pendaftar,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Selamat! Kamu Dinyatakan LULUS — UKM MCI',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pendaftar-lulus',
            with: [
                'nama'     => $this->pendaftar->nama,
                'divisi'   => $this->pendaftar->divisi?->nama ?? '-',
                'email'    => $this->pendaftar->email,
                'password' => PendaftarService::DEFAULT_PASSWORD,
                'angkatan' => $this->pendaftar->angkatan,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
