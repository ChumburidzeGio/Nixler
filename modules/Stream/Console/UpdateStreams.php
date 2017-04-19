<?php

namespace Modules\Stream\Console;

use Illuminate\Console\Command;
use Modules\Stream\Repositories\StreamRepository;

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
    public function __construct(StreamRepository $repository)
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
        $this->repository->refreshStreams();
    }

}



















