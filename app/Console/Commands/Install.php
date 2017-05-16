<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RecommService;
use App\Entities\User;
use DB, Storage, Bouncer;

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
        if ($this->confirm('Do you want to reset the system?')) {
            $this->reset();
        }

        if ($this->confirm('Do you want to seed data in database for testing?')) {
            $this->setFakeData();
        }

        $this->call('optimize');
        $this->call('cache:clear');
        
    }

    /**
     * Remove all tables from database
     *
     * @return mixed
     */
    private function reset()
    {
        $this->resetDB();
        $this->cleanStorage();
        $this->resetRecomm();

        foreach (config('app.countries') as $country) {
            $this->call('countries:download', [ 'iso_code' => $country]);
            $this->info('Populated Geo data about ' . $country);
        }

        $this->call('geoip:update');
        $this->info('Updated MaxMind database');

        $this->call('db:seed', [ '--class' => 'CategoryDatabaseSeeder' ]);
        $this->call('db:seed', [ '--class' => 'BlogDatabaseSeeder' ]);

        $this->createAccountsAndRoles();
    }

    /**
     * Remove all tables from database
     *
     * @return mixed
     */
    private function resetRecomm()
    {
        (new RecommService)->addProps();
    }

    /**
     * Remove all tables from database
     *
     * @return mixed
     */
    private function resetDB()
    {

        $tables = DB::select('SHOW TABLES');
        $droplist = [];

        foreach($tables as $table) {
            $droplist[] = $table->{'Tables_in_' . env('DB_DATABASE')};
        }

        if(!$droplist) return false;

        $droplist = implode(',', $droplist);

        DB::beginTransaction();
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::statement("DROP TABLE $droplist");
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        DB::commit();

        $this->comment("All tables successfully dropped");

        $this->call('migrate');
        $this->call('scout:mysql-index', [ 'model' => 'App\\Entities\\Product' ]);
        $this->call('scout:mysql-index', [ 'model' => 'App\\Entities\\User' ]);

    }

    /**
     * Remove all files from storage
     *
     * @return mixed
     */
    private function cleanStorage()
    {
        $files = Storage::allFiles('public/users');

        collect($files)->map(function ($value, $key) {

            return \Storage::delete($value) 
                ? $this->info('Deleted '.$value) 
                : $this->error('Can\'t delete '.$value);

        });

    }

    /**
     * Remove all files from storage
     *
     * @return mixed
     */
    private function createAccountsAndRoles()
    {
        $nixler = User::create([
            'name' => 'Nixler',
            'email' => 'info@nixler.pl',
            'password' => bcrypt('Yamaha12'),
            'username' => 'nixler',
        ]);

        Bouncer::allow('root')->to('create-articles');
        Bouncer::allow('root')->to('impersonate');

        $nixler->assign('root');
        
        Bouncer::refresh();
    }

    /**
     * Remove all tables from database
     *
     * @return mixed
     */
    private function setFakeData()
    {
        app()->setLocale(config('app.fallback_locale'));
        $this->call('db:seed');
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

        $this->info('Enviroment variables has been succesfully set');

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
