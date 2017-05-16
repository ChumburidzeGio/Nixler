<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\LocationRepository;

class DownloadCountryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'countries:download {iso_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download data, regions and cities for particular country';

    /**
     * @var object
     */
    protected $repository;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function __construct(LocationRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->repository->downloadCountry($this->argument('iso_code'));
    }

}