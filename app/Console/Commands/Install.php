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
    protected $description = 'Setup system';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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

        $this->createAccountsAndRoles();

        $this->call('optimize');
        $this->call('cache:clear');
        
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

}
