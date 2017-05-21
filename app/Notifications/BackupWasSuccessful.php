<?php

namespace App\Notifications;

use Spatie\Backup\Notifications\Notifications\BackupWasSuccessful as BaseNotification;
use App\Channels\GoogleDriveChannel;

class BackupWasSuccessful extends BaseNotification
{
    public function toGoogleDrive($notifiable)
    {
    	if (app()->environment('local', 'testing')) {
    		return false;
    	}

    	$path = storage_path('app/'.$this->backupDestination()->newestBackup()->path());

    	return [
    		'name' => env('APP_DOMAIN').'-'.config('app.env').'-'.pathinfo($path, PATHINFO_FILENAME),
    		'path' => $path
    	];
    }
}
