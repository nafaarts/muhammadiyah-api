<?php

namespace App\Http\Middleware;

use Closure;

class Token
{
    public function handle($request, Closure $next)
    {
        $token = env('TOKEN');

        if ($request->header('TOKEN') == $token) {
            return $next($request);
        }
        return response('Unauthorized.', 401);
    }
}
