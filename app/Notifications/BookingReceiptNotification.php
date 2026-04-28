<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class BookingReceiptNotification extends Notification
{
    use Queueable;

    protected $bookings; // collection or array of bookings
    protected $type; // 'booking' or 'extension'
    protected $extension; // optional extension

    public function __construct($bookings, $type = 'booking', $extension = null)
    {
        $this->bookings = $bookings;
        $this->type = $type;
        $this->extension = $extension;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $first = is_iterable($this->bookings) ? collect($this->bookings)->first() : null;
        $name = $first->name ?? ($notifiable->name ?? 'Guest');

        $mail = (new MailMessage)
            ->subject('Booking Receipt')
            ->greeting('Hello ' . $name . ',')
            ->line('Thank you for your booking. Below are the receipt details:');

        $items = collect($this->bookings)->map(function ($b) {
            $type = isset($b->room) ? 'Room' : (isset($b->boat) ? 'Boat' : 'Booking');
            $title = isset($b->room) ? optional($b->room)->room_name : (isset($b->boat) ? optional($b->boat)->name : (property_exists($b, 'id') ? ('Booking #' . $b->id) : 'Item'));
            $dates = '';
            if (!empty($b->start_date) || !empty($b->end_date)) {
                $dates = trim(($b->start_date ?? '') . ' to ' . ($b->end_date ?? ''));
            } elseif (!empty($b->booking_date)) {
                $dates = $b->booking_date . ' ' . ($b->start_time ?? '');
            }
            $line = $type . ': ' . $title;
            if ($dates)
                $line .= ' (' . $dates . ')';
            $amount = number_format((float) ($b->paid_amount ?? $b->total_amount ?? 0), 2);
            $line .= ' — ₱' . $amount;
            return $line;
        })->all();

        foreach ($items as $it) {
            $mail->line($it);
        }

        if ($this->type === 'extension' && $this->extension) {
            $mail->line('Extension Details:');
            $mail->line('Hours: ' . ($this->extension->hours ?? '-'));
            $mail->line('Amount: ₱' . number_format((float) ($this->extension->price ?? 0), 2));
            if (!empty($this->extension->new_checkout_at)) {
                $mail->line('New checkout: ' . $this->extension->new_checkout_at->toDateTimeString());
            }
        }

        $mail->line('If you have questions, reply to this email or contact us via the frontdesk.');
        $mail->line('Thank you for choosing us.');

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [];
    }
}
