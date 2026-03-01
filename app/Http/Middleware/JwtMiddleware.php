<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }
        } catch (Exception $e) {

            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        return $next($request);
    }
}
