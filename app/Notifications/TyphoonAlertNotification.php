<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TyphoonAlertNotification extends Notification
{
    use Queueable;

    protected $alert;

    public function __construct($alert)
    {
        $this->alert = $alert;
    }

    public function via($notifiable)
    {
        // Deliver alerts in-app via the database channel. Mail is optional.
        $channels = ['database'];
        if (!empty($this->alert->send_email)) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toDatabase($notifiable)
    {
        return [
            'alert_id' => $this->alert->id ?? null,
            'title' => $this->alert->title ?? null,
            'severity' => $this->alert->severity ?? null,
            'message' => $this->alert->message ?? null,
            'location' => $this->alert->location ?? null,
            'starts_at' => isset($this->alert->starts_at) ? $this->alert->starts_at->toDateTimeString() : null,
        ];
    }

    public function toMail($notifiable)
    {
        $title = $this->alert->title ?? ucfirst($this->alert->severity ?? 'Alert');

        return (new MailMessage)
            ->subject("Alert: {$title}")
            ->greeting('Important Weather Alert')
            ->line($this->alert->message ?? '')
            ->line('Location: ' . ($this->alert->location ?? 'All'))
            ->action('View Alerts', url('/'))  // Redirect to home
            ->line('Stay safe.');
    }

}
