<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SystemService;
use App\Notifications\ServerStatus;

class SystemMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications with server monitoring information';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $monitors = app(\App\Monitors\MonitorFactory::class)->get();

        if($monitors['hasErrors']) {

            $fields = array_get($monitors, 'fields')->toArray();

            app(SystemService::class)->notify(new ServerStatus($fields));

        }
    }

}
