<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Facades\Request;

class ServerStatus extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Get the notification channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via()
    {
        return ['slack'];
    }
    
    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack(): SlackMessage
    {
        return (new SlackMessage)->attachment(function ($attachment) {
            $attachment->fields($this->data)->markdown(['fields']);
        });
    }

}
