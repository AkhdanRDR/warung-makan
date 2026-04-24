<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenIdle
{
    public function handle(Request $request, Closure $next)
    {
        // Only check if user is authenticated
        if (!$request->user()) {
            return $next($request);
        }

        $token = $request->user()->currentAccessToken();

        if (!$token) {
            return $next($request);
        }

        // Check if token is a database token (not a TransientToken)
        // TransientToken is used in testing and doesn't have last_used_at
        if (!method_exists($token, 'getAttribute')) {
            return $next($request);
        }

        // Check if token hasn't been used in the last 5 days (inactivity)
        if ($token->last_used_at && $token->last_used_at->diffInDays(now()) >= 5) {
            $token->delete();

            return response()->json([
                'message' => 'Token expired due to inactivity. Please login again.'
            ], 401);
        }

        return $next($request);
    }
}
