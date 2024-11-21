<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsInactiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->is_Inactive==0) {
            return response()->json(['error' => 'Tu cuenta fue inactivada'], 401);
        }
        return $next($request);    }
}
