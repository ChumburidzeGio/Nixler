<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Channels\MessagesChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CommentedOnProduct extends Notification
{
    use Queueable;

    private $actor;

    private $product;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($actor, $product)
    {
        $this->actor = $actor;

        $this->product = $product;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', MessagesChannel::class];
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
                    ->subject($this->getMessageText(false))
                    ->line($this->getMessageText(false))
                    ->line($this->product->title)
                    ->action(__('Go to product page'), $this->product->url())
                    ->line(__('Thank you for using our application.'));
    }

    /**
     * Get the Telegram representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\Telegram\TelegramMessage
     */
    public function toMessages($notifiable)
    {
        return [
            'from' => 1,
            'to' => $notifiable->id,
            'message' => $this->getMessageText()
        ];
    }

    /**
     * Get the Telegram representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\Telegram\TelegramMessage
     */
    public function getMessageText($markdown = true)
    {
        $title = str_limit($this->product->title, 30);

        $url = $this->product->url();

        return __(':actor commented on your product :product', [
            'actor' => $this->actor->name,
            'product' => $markdown ? "\"[{$title}]({$url})\"" : ''
        ]);
    }
}
