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

        info(get_class($e).' in '.$e->getFile().' line '.$e->getLine().' with message '.$e->getMessage());

        /*try {
           $this->notify(new ExceptionThrown($e));
        } 

        catch (Exception $e) {
           info($e->getMessage());
        }*/
        
    }
}