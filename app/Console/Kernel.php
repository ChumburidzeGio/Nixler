<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;
use App\Repositories\MessengerRepository;
use App\Monitors\MonitorFactory;
use App\Services\BackupService;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CollectionsDiagnose::class,
        \App\Console\Commands\CrawlSet::class,
        \App\Console\Commands\Install::class,
        \App\Console\Commands\Update::class,
        \App\Console\Commands\Env::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call('\App\Console\Kernel@dailySchedule')->daily();

        $schedule->call('\App\Console\Kernel@hourlySchedule')->hourly();

        $schedule->call('\App\Console\Kernel@everyTenMinutesSchedule')->everyTenMinutes();

        $schedule->command('crawler:updateAll')->daily();
    }

    /**
     * Commands to be executed hourly
     *
     * @return void
     */
    public function hourlySchedule()
    {
        //app(BackupService::class)->export();

        info('Daily schedule executed succesfully.');
    }

    /**
     * Commands to be executed daily
     *
     * @return void
     */
    public function dailySchedule()
    {
        app(MessengerRepository::class)->updateResponseTimes();
        
        app(BackupService::class)->cleanOldBackups();

        info('Daily schedule executed succesfully.');
    }

    /**
     * Commands to be executed every ten minutes
     *
     * @return void
     */
    public function everyTenMinutesSchedule()
    {
        app(UserRepository::class)->updateStreams();

        app(MonitorFactory::class)->get();

        info('Every ten minutes schedule executed succesfully.');
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
