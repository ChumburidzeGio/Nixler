<?php

namespace Modules\User\Http\Console;

use Illuminate\Console\Command;
use Bouncer;

class CreateRolesAndAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:roles {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create roles and attach it to user with ID x';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Bouncer::allow('admin')->to('manage_system');

        $user = app()->make(config('auth.providers.users.model'))->findOrFail($this->argument('id'));

        Bouncer::assign('admin')->to($user);
        
    }
}