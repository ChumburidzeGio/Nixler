<?php

namespace Modules\Order\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class OrderStatusChanged extends Notification
{
    use Queueable;

    /**
     * Get the notification channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }


    /**
     * Get the Telegram representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\Telegram\TelegramMessage
     */
    public function toTelegram($notifiable)
    {
        if($notifiable->status == 'created'){
            $buyer_text = 'Thank you for placing order, we sent notification to merchant and you will get notification as soon as they will confirm the order.';
            $seller_text = 'Someone bought your product! Please go to order page and confirm the order. If the product is out of stock or you cant deliver you can also cancel the order.';
        }
        
        return TelegramMessage::create()
            ->to('-202561791')
            ->content("Order published: \n")
            ->button("Open product", $notifiable->url());

        return TelegramMessage::create()
            ->to('-202561791')
            ->content("Order published: \n")
            ->button("Open product", $notifiable->url());
    }

}