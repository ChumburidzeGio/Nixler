<?php

namespace App\Services;

use Storage;
use File, DB;
use Chumper\Zipper\Zipper;

class BackupService
{
    private $zipper;
    
    public function __construct()
    {
        $this->zipper = app(Zipper::class);
    }

    public function export()
    {
        $time = strtotime('now');

        $path = storage_path("app/backups/{$time}.zip");

        $sqlPath = storage_path("app/backups/{$time}.sql");

        $this->dumpMysql($sqlPath);

        $this->zipper->make($path)->add($sqlPath);
        
        $this->zipper->zip($path)->folder('files')->add(storage_path('app/public'));

        $this->zipper->close();

        $status = $this->zipper->zip($path)->getStatus();

        if (app()->environment('development', 'production')) 
        {
            Storage::disk('google')->put(env('APP_DOMAIN').'-'.pathinfo($sqlPath, PATHINFO_BASENAME), file_get_contents($sqlPath));
        }

        if($status == 'No error')
        {
            File::delete($sqlPath);
        }

        return $path;
    }

    public function import($path = null)
    {
    	if(is_null($path))
    	{
    		$path = $this->latestBackupPath();
    	}
    	
        $this->restoreMysql($path);

        $directory = storage_path('app/public');

        File::makeDirectory($directory, 0711, true, true); 

        File::deleteDirectory($directory);

        File::makeDirectory($directory, 0711, true, true); 

        $this->zipper->make($path)->folder('files')->extractTo($directory);
    }

    public function cleanOldBackups()
    {
        $directory = storage_path('app/backups');

        $files = File::files($directory);

        $junk = array_filter($files, function($file) {

            $time = pathinfo($file, PATHINFO_FILENAME);

            if(!is_numeric($time)) {
                return true;
            }

            $oneWeekAgo = strtotime('1 week ago');

            if($time < $oneWeekAgo) {
                return true;
            }

            return false;

        });

        File::delete($junk);
    }

    public function latestBackupPath()
    {
    	$directory = storage_path('app/backups');

        $files = File::files($directory);

        usort($files, function($a, $b) {

        	$first = pathinfo($a, PATHINFO_FILENAME);

        	$second = pathinfo($b, PATHINFO_FILENAME);

		    return is_numeric($first) && is_numeric($second) ? $second - $first : null;

		});

        return array_first($files);
    }

    public function dumpMysql($sqlPath)
    {
        $host = config('database.connections.mysql.host');

        $username = config('database.connections.mysql.username');

        $password = config('database.connections.mysql.password');

        $database = config('database.connections.mysql.database');

        $command = "mysqldump -h {$host} -u {$username} -p'{$password}' {$database} > {$sqlPath}";

        exec($command);
    }

    public function restoreMysql($latestBackupPath)
    {
        $sqlFileContent = $this->zipper->zip($latestBackupPath)->getFileContent('dump.sql');

        if($sqlFileContent)
        {
            DB::unprepared($sqlFileContent);
        }
    }
}