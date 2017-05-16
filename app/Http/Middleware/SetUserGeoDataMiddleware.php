<?php

namespace App\Http\Middleware;

use Closure, Auth;
use App\Services\LocationService;
use App\Entities\UserSession;

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
        
        UserSession::log();

        return $next($request);

    }
}
