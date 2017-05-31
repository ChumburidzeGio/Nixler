<?php

namespace App\Services;

use App\Notifications\ExceptionThrown;
use Exception, Debugbar;

class SystemService
{
    public function notify($notifcation) {
        app(config('laravel-backup.notifications.notifiable'))->notify($notifcation);
    }

    public function reportException($e) {

        if (app()->environment('testing', 'local')) {
            return Debugbar::addThrowable($e);
        } 

        try {
           $this->notify(new ExceptionThrown($e));
        } 

        catch (Exception $e) {
           info($e->getMessage());
        }
        
    }
}