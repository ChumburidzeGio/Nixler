<?php

namespace Modules\User\Providers;

use Validator, Hash;
use Illuminate\Support\ServiceProvider;
use Modules\User\Observers\UserObserver;
use Modules\User\Observers\PhoneObserver;
use Modules\User\Entities\User;
use Modules\User\Entities\Phone;
use App\Services\Phone as PhoneService;

class UserServiceProvider extends ServiceProvider
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
        User::observe(UserObserver::class);
        Phone::observe(PhoneObserver::class);
        
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        
        Validator::extend('ownpass', function ($attribute, $value, $parameters) {
            return (auth()->check() && Hash::check($value, auth()->user()->password));
        });
        
        Validator::extend('phone', function ($attribute, $value, $parameters) {

            $phone = PhoneService::parse($value, array_first($parameters));

            return $phone->is_valid;
            
        });
        
        Validator::extend('phone_unique', function ($attribute, $value, $parameters) {

            $phone = PhoneService::parse($value, array_first($parameters));

            return !Phone::where('country_code', $phone->country_code)->whereNumber($phone->number)->exists();

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
            __DIR__.'/../Config/config.php' => config_path('user.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'user'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/user');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/user';
        }, \Config::get('view.paths')), [$sourcePath]), 'user');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/user');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'user');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'user');
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
