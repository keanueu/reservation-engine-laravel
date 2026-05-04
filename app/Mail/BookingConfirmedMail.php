<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // Retry up to 3 times if SMTP fails
    public int $tries = 3;

    // Wait 60 seconds between retries
    public int $backoff = 60;

    /**
     * @param  array  $bookings   All Booking / BoatBooking models in this group
     * @param  float  $totalPaid  Deposit amount actually charged
     * @param  string $groupId    UUID shared by all bookings in this checkout
     */
    public function __construct(
        public readonly array  $bookings,
        public readonly float  $totalPaid,
        public readonly string $groupId,
    ) {
        // Send on the 'mail' queue so it doesn't block the default queue
        $this->onQueue('mail');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your booking is confirmed — Cabanas Beach Resort',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
