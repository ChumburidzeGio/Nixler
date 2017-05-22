<?php

namespace App\Services;

use AlgoliaSearch\Client;
use App\Notifications\ExceptionThrown;

class SystemService
{
    public function notify($notifcation) {
        app(config('laravel-backup.notifications.notifiable'))->notify($notifcation);
    }

    public function reportException($e) {
        $this->notify(new ExceptionThrown($e));
    }
}