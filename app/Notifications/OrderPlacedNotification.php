<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Maison De Mystere order {$this->order->order_number}")
            ->greeting('Thank you for your order')
            ->line("Order total: {$this->order->currency} ".number_format((float) $this->order->total, 2))
            ->line("UAE VAT: {$this->order->currency} ".number_format((float) $this->order->vat_total, 2))
            ->action('View order', route('checkout.confirmation', ['locale' => app()->getLocale(), 'order' => $this->order]));
    }
}
