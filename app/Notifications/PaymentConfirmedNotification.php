<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array { return ['database']; }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'payment_confirmed',
            'title'        => 'Payment Confirmed',
            'body'         => "Payment of {$this->order->formatted_total} for order #{$this->order->order_number} has been confirmed.",
            'order_id'     => $this->order->id,
            'url'          => $notifiable->id === $this->order->buyer_id
                ? route('buyer.orders.show', $this->order->id)
                : route('seller.orders.show', $this->order->id),
        ];
    }
}
