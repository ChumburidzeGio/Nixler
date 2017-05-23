<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use Bouncer, Validator;
use App\Observers\OrderObserver;
use App\Entities\Order;
use App\Observers\ActivityObserver;
use App\Entities\Activity;
use App\Observers\UserObserver;
use App\Entities\User;
use App\Services\PhoneService;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Illuminate\Http\Response;

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
        Activity::observe(ActivityObserver::class);

        Schema::defaultStringLength(191);

        Bouncer::cache();

        ResponseFacade::macro('photo', function ($value, $media, $cache = 'max-age=86400') {
            $response = ResponseFacade::make($value);
            $response->header('Content-Type', 'image/jpg');
            $response->header('content-transfer-encoding', 'binary');
            $response->header('Pragma', 'public');
            $response->header('Cache-Control', $cache);
            $response->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
            $response->header('Last-Modified', ($media ? $media->updated_at->format('D, d M Y H:i:s \G\M\T') : time()));
            return $response;
        });
        
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
        
    }
}
