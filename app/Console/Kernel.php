<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Install::class,
        \App\Console\Commands\Update::class,
        \App\Console\Commands\Env::class,
        \App\Console\Commands\Monitor::class,
        \App\Console\Commands\DownloadCountryData::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('nx:update', ['--full' => true])->daily();
        $schedule->command('nx:update')->everyTenMinutes();
        $schedule->command('nx:monitor')->everyTenMinutes();
        $schedule->command('backup:clean')->daily()->at('01:00');
        $schedule->command('backup:run')->daily()->at('02:00');
        $schedule->command('backup:monitor')->daily()->at('03:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
