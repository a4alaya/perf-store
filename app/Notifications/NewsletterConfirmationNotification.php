<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Maison De Mystere')
            ->line('You are subscribed to Maison De Mystere fragrance updates, UAE exclusives, and curated launches.')
            ->action('Shop new arrivals', route('products.index', ['locale' => app()->getLocale(), 'sort' => 'newest']));
    }
}
