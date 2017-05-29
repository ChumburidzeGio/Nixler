<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RecommService;
use App\Entities\User;
use DB, Storage, Bouncer;

class Env extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env {key} {value?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get/Set environment variables';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = $this->argument('key');

        $value = $this->argument('value');

        if(!$value) {
            return $this->info(env($key));
        }

        $this->env($key, $value);
    }


    /**
     * Write a new environment file with the given key.
     *
     * @param  string  $key
     * @return void
     */
    protected function env($key, $val)
    {
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->keyReplacementPattern($key),
           $key."=".$val,
            file_get_contents(app()->environmentFilePath())
        ));
    }


    /**
     * Get a regex pattern that will match env key.
     *
     * @return string
     */
    protected function keyReplacementPattern($key)
    {
        $escaped = preg_quote('='.env($key), '/');
        return "/^{$key}{$escaped}/m";
    }
}
