<?php

namespace App\Http\Middleware;

use Closure, Auth, MetaTag;
use App\Services\LocationService;

class SetUserGeoDataMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $service = new LocationService;

        $service->findLocale();

        if($request->isMethod('get')){

            MetaTag::set('title', __('Buy and Sell Online Clothes, Shoes, Electronics & more'));

            MetaTag::set('description', __('Sign up and find the best offers from shops in your area or become a seller and get the new channel of sales for free.'));

            MetaTag::set('image', url('/img/meta.jpg'));

            MetaTag::set('type', 'website');

        }
        
        return $next($request);

    }
}
