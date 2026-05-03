<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array  $bookings   All Booking / BoatBooking models in this group
     * @param  float  $totalPaid  Deposit amount actually charged
     * @param  string $groupId    UUID shared by all bookings in this checkout
     */
    public function __construct(
        public readonly array  $bookings,
        public readonly float  $totalPaid,
        public readonly string $groupId,
    ) {}

    public function envelope(): Envelope
    {
        $name = $this->bookings[0]->name ?? 'Guest';

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
