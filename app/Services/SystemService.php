<?php

namespace App\Services;

use AlgoliaSearch\Client;
use App\Notifications\ExceptionThrown;
use Exception;

class SystemService
{
    public function notify($notifcation) {
        app(config('laravel-backup.notifications.notifiable'))->notify($notifcation);
    }

    public function reportException($e) {
        try {
        	$this->notify(new ExceptionThrown($e));
        } catch (Exception $e) {
        	info($e->getMessage());
        }
    }
}