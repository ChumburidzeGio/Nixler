<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\HtmlMinifierService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MinifyResponse
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if (!app()->isLocal() and false === is_a($response, StreamedResponse::class) and $response->headers->get('Content-Type') !== 'image/jpg') {
            $response->setContent((new HtmlMinifierService())->minify($response->getContent()));
        }

        return $response;
    }
}
