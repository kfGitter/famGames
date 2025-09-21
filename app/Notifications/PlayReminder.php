<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlayReminder extends Notification
{
    use Queueable;

    protected string $frequency; // daily or weekly

    public function __construct(string $frequency = 'daily')
    {
        $this->frequency = $frequency;
    }

    public function via(object $notifiable): array
    {
        return ['mail']; // ✅ only mail
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("FamPlay {$this->frequency} reminder 🎲")
            ->greeting("Hello {$notifiable->name},")
            ->line("It's time for your {$this->frequency} family game session! 🎉")
            ->line("Play together today to keep your streak alive and stay consistent.")
            ->action('Go to FamPlay', url('/dashboard'))
            ->line('Have fun bonding with your family!');
    }
}
