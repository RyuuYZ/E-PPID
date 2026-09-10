<?php

namespace App\Mail;

use App\Models\PermohonanInformasi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PermohonanTerkirimMail extends Mailable
{
    use Queueable, SerializesModels;

    public PermohonanInformasi $permohonan;

    /**
     * Create a new message instance.
     */
    public function __construct(PermohonanInformasi $permohonan)
    {
        $this->permohonan = $permohonan;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bukti Pendaftaran Permohonan Informasi Publik [' . $this->permohonan->nomor_registrasi . '] - PPID Kabupaten Ciamis',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.permohonan_terkirim',
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
