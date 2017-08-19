<?php

namespace App\Services;

use App\Notifications\ExceptionThrown;
use Exception, Debugbar;

class SystemService
{
    public function notify($notifcation) {
        app(\App\Notifications\Notifiables\Notifiable::class)->notify($notifcation);
    }

    public function reportException($e) {

        try {
           $this->notify(new ExceptionThrown($e));
        } 

        catch (Exception $e) {
           info($e->getMessage());
        }
        
    }
}