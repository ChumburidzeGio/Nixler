<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;
use App\Repositories\MessengerRepository;
use App\Monitors\MonitorFactory;

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

        $schedule->call('\App\Console\Kernel@everyTenMinutesSchedule')->everyTenMinutes();

        $schedule->command('backup:clean')->daily()->at('01:00');
        $schedule->command('backup:run')->daily()->at('02:00');
        $schedule->command('backup:monitor')->daily()->at('03:00');

        $schedule->command('crawler:updateAll')->hourly();
    }

    /**
     * Commands to be executed daily
     *
     * @return void
     */
    public function dailySchedule()
    {
        app(MessengerRepository::class)->updateResponseTimes();

        app(ProductRepository::class)->cleanStorage();

        app(UserRepository::class)->updateAnalytics();

        app(ProductRepository::class)->updateAnalytics();

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
