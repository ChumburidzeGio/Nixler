<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use Bouncer, Validator;
use App\Observers\OrderObserver;
use App\Entities\Order;
use App\Observers\ProductObserver;
use App\Entities\Product;
use App\Observers\ActivityObserver;
use App\Entities\Activity;
use App\Observers\UserObserver;
use App\Entities\User;
use App\Services\PhoneService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Order::observe(OrderObserver::class);
        Product::observe(ProductObserver::class);
        Activity::observe(ActivityObserver::class);

        Schema::defaultStringLength(191);

        Bouncer::cache();

        
        Validator::extend('ownpass', function ($attribute, $value, $parameters) {
            return (auth()->check() && Hash::check($value, auth()->user()->password));
        });
        
        Validator::extend('phone', function ($attribute, $value, $parameters) {

            $phone = PhoneService::parse($value, array_first($parameters));
            return $phone->is_valid;
            
        });
        
        Validator::extend('phone_unique', function ($attribute, $value, $parameters) {

            $phone = PhoneService::parse($value, array_first($parameters));

            return !User::where('phone', $phone->number)->where('id', '<>', auth()->id())->exists();

        });
        
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('production', 'development')) {
            $this->app->alias('bugsnag.logger', \Illuminate\Contracts\Logging\Log::class);
            $this->app->alias('bugsnag.logger', \Psr\Log\LoggerInterface::class);
        }
    }
}
