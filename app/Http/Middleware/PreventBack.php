<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class PreventBack
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, private, post-check=0, pre-check=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Clear-Site-Data', '"cache", "cookies", "storage"');
        $response->headers->set('Cache-Control', 'private, no-cache, no-store, must-revalidate');
        $response->headers->set('Clear-Site-Data', '"cache", "cookies", "storage", "executionContexts"');

        return $response;
    }
}
