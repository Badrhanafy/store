<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'order_cancelled',
            'message' => "Order #{$this->order->id} has been cancelled by {$this->order->customer_name}",
            'order_id' => $this->order->id,
            'customer_name' => $this->order->customer_name,
            'customer_phone' => $this->order->phone,
            'total_amount' => $this->order->total_price,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Order #{$this->order->id} Cancelled")
            ->line("Order #{$this->order->id} has been cancelled by {$this->order->customer_name}")
            ->line("Customer: {$this->order->customer_name}")
            ->line("Phone: {$this->order->phone}")
            ->line("Total Amount: {$this->order->total_price}")
            ->action('View Order', url("/admin/orders/{$this->order->id}"))
            ->line('Thank you for using our service!');
    }
}