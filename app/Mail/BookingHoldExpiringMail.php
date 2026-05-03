<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class BookingHoldExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Booking  $booking     The first booking in the group (for guest info)
     * @param  int      $minutesLeft Minutes remaining before the hold expires
     * @param  string   $groupId     UUID for the payment link
     */
    public function __construct(
        public readonly Booking $booking,
        public readonly int     $minutesLeft,
        public readonly string  $groupId,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Your room hold expires in ' . $this->minutesLeft . ' minutes — complete your booking now',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-hold-expiring',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
