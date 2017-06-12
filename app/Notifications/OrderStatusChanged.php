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
use Illuminate\Notifications\Messages\SlackMessage;

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
        return ['slack', MessagesChannel::class];
    }


    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toSlack($notifiable): SlackMessage
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

        return (new SlackMessage)->attachment(function ($attachment) use ($notifiable) {
            $attachment->title("Order status changed to " . $notifiable->status, $notifiable->url())->fields([
                'Environment' => config('app.env'),
                'User' => "#".auth()->id()." (".auth()->user()->name.")"
            ]);
        });

    }

    /**
     * Get the Telegram representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\Telegram\TelegramMessage
     */
    public function toMessages($notifiable)
    {
        switch ($notifiable->status) {
            case 'created':
                $message = "Someone bought your product! Please go to [order page](:url) and confirm the order. If the product is out of stock or you cant deliver you can also cancel the order.";
                break;
                
            case 'confirmed':
                $message = "Merchant confirmed your order. Please go to [order page](:url) for more information.";
                break;
                
            case 'rejected':
                $message = "Merchant rejected your order. Please go to [order page](:url) for more information.";
                break;
                
            case 'sent':
                $message = "Merchant set status of your order as sent. Please go to [order page](:url) for more information.";
                break;

            case 'closed':
                $message = "Merchant closed your order. Please go to [order page](:url) for more information.";
                break;
        }

        return [
            'from' => 1,
            'to' => ($notifiable->status == 'created') ? $notifiable->merchant()->first()->id : $notifiable->user()->first()->id,
            'message' => __($message, ['url' => $notifiable->url()]) 
        ];

    }

}