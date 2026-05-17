<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FixAuthorizationHeader
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if ($authHeader && !str_starts_with($authHeader, 'Bearer ') && str_contains($authHeader, '|')) {
            $request->headers->set('Authorization', 'Bearer ' . $authHeader);
        }

        return $next($request);
    }
}
