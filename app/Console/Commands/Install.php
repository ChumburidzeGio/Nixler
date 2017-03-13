<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
            $this->info('Database is almost clean!');
        }

        //if ($this->confirm('Do you want to clean all storage?')) {
        //    $this->cleanStorage('users');
        //}

        if ($this->confirm('Do you want to update MaxMind IP database and download countries data?')) {
            $this->setGeo();
            $this->info('Geo data updated and added to database');
        }

        if ($this->confirm('Do you want to seed data to database for testing?')) {
            $this->setFakeData();
            $this->info('Fake data added to database');
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

        if($env = $this->anticipate('What is the enviroment?', ['local', 'production', 'development'])){
            $this->writeNewEnvironmentFileWith('APP_ENV', $env);
        }

        if($db_name = $this->ask('Name of database?')){
            $this->writeNewEnvironmentFileWith('DB_DATABASE', $db_name);
        }

        if($db_port = $this->ask('Port?')){
            $this->writeNewEnvironmentFileWith('DB_PORT', $db_port);
        }

        if($db_user = $this->ask('Username?')){
            $this->writeNewEnvironmentFileWith('DB_USERNAME', $db_user);
        }

        if($db_pass = $this->secret('Password?')){
            $this->writeNewEnvironmentFileWith('DB_PASSWORD', $db_pass);
        }

        if($mg_sec = $this->ask('Mailgun secret?')){
            $this->writeNewEnvironmentFileWith('MAILGUN_SECRET', $mg_sec);
        }

        if($fb_id = $this->ask('Facebook APP ID?')){
            $this->writeNewEnvironmentFileWith('FACEBOOK_APP_ID', $fb_id);
        }

        if($fb_secret = $this->ask('Facebook APP secret?')){
            $this->writeNewEnvironmentFileWith('FACEBOOK_APP_SECRET', $fb_secret);
        }

        if($mc_key = $this->ask('Mailchimp API key?')){
            $this->writeNewEnvironmentFileWith('MAILCHIMP_APIKEY', $mc_key);
        }

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
    private function setGeo()
    {
        $this->call('address:install');

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
    protected function writeNewEnvironmentFileWith($key, $val)
    {
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->keyReplacementPattern($key),
           $key."=".$val,
            file_get_contents(app()->environmentFilePath())
        ));
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
