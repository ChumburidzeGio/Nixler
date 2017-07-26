<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Channels\MessagesChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ThankYouOnOrder extends Notification
{
    use Queueable;

    private $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(__('Your Order Confirmation'))
                    ->line(__('Woot! Time to celebrate! We sent notification to :shop and very soon you will get confirmation from the shop about your order!', ['shop' => $this->order->merchant->name]))
                    ->action(__('Here Is Your Order'), $this->order->url());
    }
}
