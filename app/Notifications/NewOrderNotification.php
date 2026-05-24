<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'new_order',
            'title'        => 'New Order Received',
            'body'         => "You have a new order #{$this->order->order_number} from {$this->order->buyer->name}.",
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'url'          => route('seller.orders.show', $this->order->id),
        ];
    }
}
