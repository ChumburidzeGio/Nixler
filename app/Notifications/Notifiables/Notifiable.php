<?php

namespace App\Notifications\Notifiables;

use Illuminate\Notifications\Notifiable as NotifiableTrait;

class Notifiable
{
    use NotifiableTrait;

    public function routeNotificationForSlack()
    {
        return env('SERVER_MONITOR_SLACK_WEBHOOK_URL');
    }

    public function getKey()
    {
        return 1;
    }
}
