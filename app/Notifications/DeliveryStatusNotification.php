<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliveryStatusNotification extends Notification implements ShouldQueue
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
            ->subject("Delivery update for {$this->order->order_number}")
            ->line("Delivery status: {$this->status}")
            ->line('Maison De Mystere will keep you updated as your fragrance moves through fulfilment.');
    }
}
