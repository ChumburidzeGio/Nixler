<?php

namespace Modules\Address\Console;

use Illuminate\Console\Command;
use Modules\Address\Services\LocationService;

class CreateGeoDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'address:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database file for GEO';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new LocationService)->updateDatabase();
    }
}