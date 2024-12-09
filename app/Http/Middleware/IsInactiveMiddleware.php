<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Usuario as User;

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
    // Sacamos el email del request
    $email = $request->input('email');
     
    // Si no hay un email en el request, intentamos obtenerlo del usuario autenticado
    if ($email == null) {
        // Comprobamos si el usuario está autenticado
        if (Auth::check()) {
            // Si el usuario está autenticado, obtenemos su email
            $email = Auth::user()->email;
        } else {
            // Si no está autenticado, devolvemos un error indicando que no se proporcionaron datos de usuario
            return response()->json(['message' => 'Datos de usuario no proporcionados.'], 401);
        }
    }

    // Verificamos si el usuario existe en la base de datos
    $user = User::where('email', $email)->first();

    // Si el usuario no existe, devolvemos un mensaje indicando que los datos del usuario no son válidos
    if ($user == null) {
        return response()->json(['message' => 'Datos de usuario no proporcionados.'], 404);
    }

    // Verificamos si el usuario está desactivado
    if ($user->is_Inactive==0) {
        // Si el usuario está desactivado, devolvemos un error indicando acceso denegado
        return response()->json(['message' => 'Acceso denegado, el usuario fue desactivado.'], 403);
    }

    // Si todo es correcto, continuamos con la solicitud
    return $next($request);
    }
}
