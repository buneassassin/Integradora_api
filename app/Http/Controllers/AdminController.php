<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    public function performAction()
    {
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'message' => 'Admin info retrieved successfully',
            'user' =>[
                'id' => $user->id,
                'email' => $user->email,
                'usuario_nom' => $user->usuario_nom,
                'foto_perfil' => $user->foto_perfil,
                'persona' => [
                    'nombres' => $user->persona->nombres,
                    'a_p' => $user->persona->a_p,
                    'a_m' => $user->persona->a_m,
                    'telefono' => $user->persona->telefono
                ]
            ]
        ], 200);
    }
    public function obtenerUsuariosConTinacos()
    {
        $usuarios = Usuario::with(['persona', 'tinacos'])
            ->get()
            ->map(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'usuario_nom' => $usuario->usuario_nom,
                    'email' => $usuario->email,
                    'rol' => $usuario->rol,
                    'is_active' => $usuario->is_active,
                    'fecha_registro' => $usuario->created_at->toDateTimeString(),
                    'tiempo_registrado' => $usuario->created_at->diffForHumans(),
                    'numero_tinacos' => $usuario->tinacos->count(),
                    'tinacos' => $usuario->tinacos,
                    'persona' => $usuario->persona,
                ];
            });

        return response()->json($usuarios, 200);
    }
    public function desactivarUsuario(Request $request) 
    {
        // Validación: solo requerir el formato correcto de email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }
    
        // Buscar el usuario por email
        $user = Usuario::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        // Verificar si el usuario ya esta desactivado
        if ($user->is_Inactive== false) {
            return response()->json(['message' => 'El usuario ya esta desactivado.'], 400); 
        }
    
        // Desactivar al usuario (cambiar el campo `is_Inactive`)
        $user->is_Inactive = false;  // Asumo que quieres desactivar el usuario, se cambió a `true`
        $user->save();
    
        return response()->json(['message' => 'Usuario desactivado correctamente.'], 200);
    }
    
    //cambir el rol
    public function cambiarRol(Request $request)
    {
        //vereficamos si el rol existe
        if (!$request->rol) {
            return response()->json(['message' => 'El rol no existe.'], 404);
        }
        $user = Usuario::find($request->id);
        $user->rol = $request->rol;
        $user->save();
        return response()->json(['message' => 'Rol cambiado correctamente.'], 200);
    }
}
