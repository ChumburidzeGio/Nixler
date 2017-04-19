<?php

namespace Modules\User\Http\Middleware;

use Closure, Auth;
use Modules\Address\Services\LocationService;
use Modules\User\Entities\UserSession;

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
