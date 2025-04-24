<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NoAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if ($user) {
                return redirect('/dashboard'); // Jika sudah login, arahkan langsung ke dashboard
            }
        } catch (\Exception $e) {
            // Token tidak valid atau tidak ada, lanjutkan ke halaman login
        }

        return $next($request);
    }
}
