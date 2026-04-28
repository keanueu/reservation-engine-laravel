<?php
// app/Notifications/RegistrationOtpNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RegistrationOtpNotification extends Notification
{
    use Queueable;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        return (new MailMessage)
            ->from($fromAddress, $fromName)
            ->replyTo($fromAddress, $fromName)
            ->subject('Cabanas — Verify your account')
            ->greeting('Hello,')
            ->line('Thank you for registering at Cabanas. Use the one-time verification code below to complete your sign up:')
            ->line('')
            ->line($this->otp)
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not request this code, please ignore this message or contact support.')
            // Add a Return-Path header (envelope) to help some providers
            // Note: proper SPF/DKIM setup is still required for reliable delivery.
            // The withSymfonyMessage callback is used with Symfony Mailer.
            // If your Laravel installation uses SwiftMailer, replace withSwiftMessage.
            ->withSymfonyMessage(function ($message) use ($fromAddress) {
                try {
                    $headers = $message->getHeaders();
                    $headers->addTextHeader('Return-Path', $fromAddress);
                } catch (\Throwable $e) {
                    // Ignore header errors — non-fatal for delivery
                }
            });
    }
}
