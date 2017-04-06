<?php

namespace Modules\Address\Providers;

use Modules\Address\Console\DownloadCountryData;
use Modules\Address\Console\CreateGeoDatabase;
use Illuminate\Support\ServiceProvider;
use Modules\Address\Repositories\AddressRepository;
use Validator;

class AddressServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerCommands();
        $this->registerConfig();
        $this->registerViews();

        Validator::extend('postcode', function ($attribute, $value, $parameters) {
            return AddressRepository::validatePostCode($value);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('address.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'address'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/address');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/address';
        }, \Config::get('view.paths')), [$sourcePath]), 'address');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/address');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'address');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'address');
        }
    }

    /**
     * Register commands.
     *
     * @return void
     */
    public function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DownloadCountryData::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
