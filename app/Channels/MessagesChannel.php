<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Repositories\MessengerRepository;

class MessagesChannel
{
    /**
     * Notification channel to send message inside platform.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Trello\Exceptions\InvalidConfiguration
     * @throws \NotificationChannels\Trello\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $params = $notification->toMessages($notifiable);

        $messagable = array_get($params, 'to') ? : $notifiable->id;

        $sender = array_get($params, 'from');

        $message = array_get($params, 'message');

        app(MessengerRepository::class)->sendMessageById($sender, $messagable, $message);
    }
}
