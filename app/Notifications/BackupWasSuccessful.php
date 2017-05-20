<?php

namespace App\Notifications;

use Spatie\Backup\Notifications\Notifications\BackupWasSuccessful as BaseNotification;
use App\Channels\GoogleDriveChannel;

class BackupWasSuccessful extends BaseNotification
{
    public function toGoogleDrive($notifiable)
    {
    	$path = storage_path('app/'.$this->backupDestination()->newestBackup()->path());

    	return [
    		'name' => pathinfo($path, PATHINFO_FILENAME),
    		'path' => $path
    	];
    }
}
