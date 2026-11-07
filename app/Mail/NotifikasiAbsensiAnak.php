<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiAbsensiAnak extends Mailable
{
    use Queueable, SerializesModels;

    public $presensi;
    public $siswa;

    public function __construct($presensi, $siswa)
    {
        $this->presensi = $presensi;
        $this->siswa = $siswa;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notifikasi Kehadiran Anak: ' . $this->siswa->nama_siswa,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notifikasi-absensi',
        );
    }
}
