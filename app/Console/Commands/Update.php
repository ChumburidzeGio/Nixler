<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\BlogRepository;
use App\Repositories\ProductRepository;
use App\Upgrade\OnePointNinetyOne;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update information on server or in database';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->legal();

        $this->searchIndex();

        $this->categories();

        $this->upgradeToLatest();
    }

    /**
     * Update legal documents on service
     *
     * @return void
     */
    public function legal()
    {
        $privacy_en = file_get_contents(resource_path('docs/privacy.en.md'));

        app(BlogRepository::class)->updateOrCreateBySlug('privacy', [
            'title:en' => 'Welcome to the Nixler Privacy Policy',
            'body:en' => $privacy_en
        ]);

        $terms_en = file_get_contents(resource_path('docs/terms.en.md'));

        app(BlogRepository::class)->updateOrCreateBySlug('terms', [
            'title:en' => 'Nixler Terms of Service',
            'body:en' => $terms_en
        ]);

    }

    /**
     * Update search indexes
     *
     * @return void
     */
    public function searchIndex()
    {
        $this->call('scout:import', ['model' => 'App\\Entities\\Product']);
        $this->call('scout:import', ['model' => 'App\\Entities\\User']);
    }

    /**
     * Update categories list in database
     *
     * @return void
     */
    public function categories()
    {
        app(ProductRepository::class)->syncCategories();
    }


    /**
     * Update categories list in database
     *
     * @return void
     */
    public function availableVersions()
    {
        return collect([
            '1.91' => OnePointNinetyOne::class
        ]);
    }


    /**
     * Update categories list in database
     *
     * @return void
     */
    public function upgradeToLatest()
    {
        $this->availableVersions()->map(function($version, $key){

            if(floatval($key) <= floatval(config('app.version'))) {
                return false;
            }

            $this->comment("Upgrading to version $key");

            app($version)->upgrade();

            $this->call('env', [
                'key' => 'APP_VERSION',
                'value' => $key
            ]);

            $this->info("Upgraded to version $key");

        });
    }

}
