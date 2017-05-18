<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use App\Emails\SoldNotificationEmail;
use App\Emails\StatusChangedNotificationEmail;
use App\Channels\MessagesChannel;
use Illuminate\Support\Facades\Mail;

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
        return [TelegramChannel::class, MessagesChannel::class];
    }


    /**
     * Get the Telegram representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\Telegram\TelegramMessage
     */
    public function toTelegram($notifiable)
    {
        switch ($notifiable->status) {
            case 'created':
                Mail::to($notifiable->merchant()->first()->email)
                    ->send(new SoldNotificationEmail($notifiable->url()));
                break;
                
            case 'confirmed':
                Mail::to($notifiable->user()->first()->email)
                    ->send(new StatusChangedNotificationEmail($notifiable->url(), $notifiable->status));
                break;
                
            case 'rejected':
                Mail::to($notifiable->user()->first()->email)
                    ->send(new StatusChangedNotificationEmail($notifiable->url(), $notifiable->status));
                break;
                
            case 'sent':
                Mail::to($notifiable->user()->first()->email)
                    ->send(new StatusChangedNotificationEmail($notifiable->url(), $notifiable->status));
                break;

            case 'closed':
                Mail::to($notifiable->merchant()->first()->email)
                    ->send(new StatusChangedNotificationEmail($notifiable->url(), $notifiable->status));
                break;
        }

        return TelegramMessage::create()
            ->to('-202561791')
            ->content("Order status changed to " . $notifiable->status .": \n")
            ->button("Open order", $notifiable->url());

    }

    /**
     * Get the Telegram representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\Telegram\TelegramMessage
     */
    public function toMessages($notifiable)
    {
        $data = [
            'from' => 1
        ];

        switch ($notifiable->status) {
            case 'created':
                $data['to'] = $notifiable->merchant()->first()->id;
                $data['message'] = "Someone bought your product! Please go to order page and confirm the order. If the product is out of stock or you cant deliver you can also cancel the order.\n".$notifiable->url();
                break;
                
            case 'confirmed':
                $data['to'] = $notifiable->user()->first()->id;
                $data['message'] = "Merchant confirmed your order. Please go to orders page for more information.\n".$notifiable->url();
                break;
                
            case 'rejected':
                $data['to'] = $notifiable->user()->first()->id;
                $data['message'] = "Merchant rejected your order. Please go to orders page for more information.\n".$notifiable->url();
                break;
                
            case 'sent':
                $data['to'] = $notifiable->user()->first()->id;
                $data['message'] = "Merchant set status of your order as sent. Please go to orders page for more information.\n".$notifiable->url();
                break;

            case 'closed':
                $data['to'] = $notifiable->merchant()->first()->id;
                $data['message'] = "The order #".$notifiable->id." is closed. Please go to orders page for more information.\n".$notifiable->url();
                break;
        }

        return $data;

    }

}