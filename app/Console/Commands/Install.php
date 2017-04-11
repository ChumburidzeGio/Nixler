<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Stream\Services\RecommService;
use DB;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        if ($this->confirm('Do you want to set enviroment variables ?')) {
            $this->setENV();
            $this->info('Enviroment variables has been succesfully set');
        }
        
        if ($this->confirm('Do you want to reset database ?')) {
            $this->setDB();
        }

        //if ($this->confirm('Do you want to clean all storage?')) {
        //    $this->cleanStorage('users');
        //}

        if ($this->confirm('Do you want to update MaxMind IP database and download countries data?')) {
            $this->setGeo();
        }

        if ($this->confirm('Set Recommender props')) {
            $this->setRecomm();
        }

        if ($this->confirm('Do you want to seed data to database for testing?')) {
            $this->setFakeData();
        }

        $this->call('optimize');
        
    }

    /**
     * Remove all tables from database
     *
     * @return mixed
     */
    private function setENV()
    {

        $this->call('key:generate');

        $env = $this->svwq('What is the environment?', 'APP_ENV', 'anticipate', ['local', 'production', 'development']);

        $this->svwq('Name of database?', 'DB_DATABASE');
        $this->svwq('Port?', 'DB_PORT');
        $this->svwq('Username?', 'DB_USERNAME');
        $this->svwq('Password?', 'DB_PASSWORD');

        if($env != 'production'){

            $this->env('MAIL_DRIVER', 'mailtrap');
            $this->env('MAIL_HOST', 'smtp.mailtrap.io');
            $this->env('MAIL_PORT', '2525');

            $this->svwq('Mailtrap username?', 'MAIL_USERNAME');
            $this->svwq('Mailtrap password?', 'MAIL_PASSWORD');

        } else {

            $this->env('MAIL_DRIVER', 'mailgun');
            $this->env('MAIL_HOST', 'smtp.mailgun.org');
            $this->env('MAIL_PORT', '587');
            $this->env('MAILGUN_DOMAIN', 'mail.nixler.pl');

            $this->svwq('Mailgun secret?', 'MAILGUN_SECRET');

        }

        $this->svwq('Facebook APP ID?', 'FACEBOOK_APP_ID');
        $this->svwq('Facebook APP secret?', 'FACEBOOK_APP_SECRET');
        $this->svwq('Facebook APP redirect link?', 'FACEBOOK_APP_REDIRECT');

        $this->svwq('Recomm DB name?', 'RECOMM_DB');
        $this->svwq('Recomm DB key?', 'RECOMM_KEY');
        
        $this->svwq('Mailchimp API key?', 'MAILCHIMP_APIKEY');

    }

    /**
     * Remove all tables from database
     *
     * @return mixed
     */
    private function setDB()
    {
        $this->cleanDB();
        $this->comment("All tables successfully dropped");
        $this->call('migrate');
        $this->call('module:migrate');
        $this->call('scout:mysql-index', [ 'model' => 'Modules\\Product\\Entities\\Product' ]);
    }

    /**
     * Remove all tables from database
     *
     * @return mixed
     */
    private function setRecomm()
    {
        (new RecommService)->addProps();
    }

    /**
     * Remove all tables from database
     *
     * @return mixed
     */
    private function setGeo()
    {
        $this->call('geoip:update');

        foreach (config('app.countries') as $country) {
            $this->call('countries:download', [ 'iso_code' => $country]);
        }
    }

    /**
     * Remove all tables from database
     *
     * @return mixed
     */
    private function setFakeData()
    {
        $this->call('db:seed', [ '--class' => 'Modules\User\Database\Seeders\SeedFakeUsersTableSeeder' ]);
        $this->call('db:seed', [ '--class' => 'Modules\Product\Database\Seeders\ProductDatabaseSeeder' ]);
    }

    /**
     * Remove all tables from database
     *
     * @return mixed
     */
    private function cleanDB()
    {

        $tables = DB::select('SHOW TABLES');

        foreach($tables as $table) {
            $droplist[] = $table->{'Tables_in_' . env('DB_DATABASE')};
        }

        $droplist = implode(',', $droplist);

        DB::beginTransaction();
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::statement("DROP TABLE $droplist");
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        DB::commit();

    }

    /**
     * Remove all files from storage
     *
     * @return mixed
     */
    private function cleanStorage($dir)
    {

        collect(\Storage::files('storage/'.$dir))->map(function ($value, $key) {

            if(!ends_with($value, 'default.jpg')){

                return \Storage::delete($value) 
                ? $this->info('Deleted '.$value) 
                : $this->error('Can\'t delete '.$value);

            } else {
                $this->info('Skipped '.$value);
            }

        });

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
     * Write a new environment file with the given key.
     *
     * @param  string  $key
     * @return void
     */
    protected function svwq($question, $key, $type = 'ask', $params = null, $val = null)
    {
        if($val = $this->{$type}($question, $params)){
            
            $this->env($key, $val);
            
        }

        return $val;
    }


    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern($key)
    {
        $escaped = preg_quote('='.env($key), '/');
        return "/^{$key}{$escaped}/m";
    }
}
