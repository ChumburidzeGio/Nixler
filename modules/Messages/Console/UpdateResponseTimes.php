<?php

namespace Modules\Messages\Console;

use Illuminate\Console\Command;
use Modules\Messages\Repositories\MessengerRepository;

class UpdateResponseTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'response_times:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update response times for all users active for last one day.';


    /**
     * @var object
     */
    protected $repository;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function __construct(MessengerRepository $repository)
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
        $this->repository->updateResponseTimes();
    }

}



















