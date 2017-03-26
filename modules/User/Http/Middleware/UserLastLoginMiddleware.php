<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Modules\User\Entities\UserSession;

class UserLastLoginMiddleware
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
        UserSession::log();

        return $next($request);
    }
}
