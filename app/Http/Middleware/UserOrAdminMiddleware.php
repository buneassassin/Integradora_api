<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserOrAdminMiddleware
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
        if (!auth()->check() || !in_array(auth()->user()->rol, ['user', 'admin'])) {
            return response()->json(['error' => 'Eres un invitado, actualmente no puedes realizar esta accion'], 401);
        }
        return $next($request);

    }
}
