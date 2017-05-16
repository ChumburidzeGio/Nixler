<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\UserRepository;

class UpdateStreams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stream:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update streams of all users';


    /**
     * @var object
     */
    protected $repository;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function __construct(UserRepository $repository)
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
        $this->repository->updateStreams();
    }

}



















