<?php

namespace App\Http\Middleware;

use Closure;

class Token
{
    public function handle($request, Closure $next)
    {

        $token = env('TOKEN');

        if (explode(' ', $request->header('Authorization'))[1] == $token) {
            return $next($request);
        }
        return response('Unauthorized.', 401);
    }
}
