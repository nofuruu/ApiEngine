<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Illuminate\Support\Facades\Redirect;

class JwtAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Cek apakah token valid
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return Redirect::to('/');
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return Redirect::to('/');
            } else {
                return Redirect::to('/');
            }
        }
        return $next($request);
    }
}
