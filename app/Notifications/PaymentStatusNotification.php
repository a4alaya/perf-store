<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public string $status)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Payment {$this->status} for {$this->order->order_number}")
            ->line("Payment status: {$this->status}")
            ->line("Order total: {$this->order->currency} ".number_format((float) $this->order->total, 2))
            ->action('View order', route('checkout.confirmation', ['locale' => app()->getLocale(), 'order' => $this->order]));
    }
}
