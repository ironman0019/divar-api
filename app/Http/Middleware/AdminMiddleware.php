<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_admin) {
            return $next($request);
        }

        // Handle unauthorized users differently for API vs Web
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'شما دسترسی لازم را ندارید',
            ], 403);
        }

        // For web routes
        abort(403, 'شما دسترسی لازم را ندارید.');
    }
}
