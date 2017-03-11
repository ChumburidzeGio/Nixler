<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Carbon\Carbon;

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
        if(auth()->check()){
            auth()->user()->setMeta('last_activity', Carbon::now());
        }

        return $next($request);
    }
}
