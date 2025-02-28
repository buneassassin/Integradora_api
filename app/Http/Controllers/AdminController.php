<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Tinaco;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function performAction()
    {
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'message' => 'Admin info retrieved successfully',
            'user' => [
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
                    'is_Inactive'=> $usuario->is_Inactive,
                    'numero_tinacos' => $usuario->tinacos->count(),
                    'tinacos' => $usuario->tinacos,
                    'persona' => $usuario->persona,
                    'foto_perfil' => $usuario->foto_perfil, // Agregar la foto de perfil
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
        if ($user->is_Inactive == false) {
            return response()->json(['message' => 'El usuario ya esta desactivado.'], 400);
        }

        // Desactivar al usuario (cambiar el campo `is_Inactive`)
        $user->is_Inactive = false;  // Asumo que quieres desactivar el usuario, se cambió a `true`
        $user->save();

        return response()->json(['message' => 'Usuario desactivado correctamente.'], 200);
    }
    public function activarUsuario(Request $request)
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
        // Verificar si el usuario ya esta activado
        if ($user->is_Inactive == true) {
            return response()->json(['message' => 'El usuario ya esta activado.'], 400);
        }

        // Activar al usuario (cambiar el campo `is_active`)
        $user->is_Inactive = true;  // Asumo que quieres desactivar el usuario, se cambió a `true`
        $user->save();

        return response()->json(['message' => 'Usuario activado correctamente.'], 200);
     
    }

    //cambir el rol
    public function cambiarRol(Request $request)
    {
        //vereficamos si el rol existe
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'rol' => 'required|in:Guest,user,Admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }

        $user = Usuario::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        //vereficamos si el usuario es admin
        if ($user->rol == 'Admin') {
            return response()->json(['message' => 'El usuario es administrador.'], 400);
        }

        $user->rol = $request->rol;
        $user->save();

        return response()->json(['message' => 'Rol cambiado correctamente.'], 200);
    }
    public function getUserStatistics()
    {
        // Total de usuarios
        $totalUsers = Usuario::count();
        

        // Usuarios activos basados en los tokens válidos (últimos 30 días)
        $bannedUsers = Usuario::where('is_Inactive', false)->count();

        // Total de usuarios baniados 
        $activeUsers= Usuario::where('is_Inactive', true)->count();

        // usuarios admin 
        $adminUsers = Usuario::where('rol', 'Admin')->count();
        // usuarios user y guest
        $userUsers = Usuario::where('rol', 'user')->count();
        $guestUsers = Usuario::where('rol', 'Guest')->count();
        $userUsers = $userUsers + $guestUsers;
        
       

        // Datos para el gráfico (usuarios registrados por mes en el último año)
        $usersByMonth = Usuario::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', '=', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Retornar los datos en formato JSON
        return response()->json([
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $bannedUsers,
            'usersByMonth' => $usersByMonth,
            'adminUsers' => $adminUsers,
            'userUsers' => $userUsers,
        ]);
    }
    public function getTinacoStatistics()
    {
        $totalTinacos = Tinaco::count();
        $tinacosByMonth = Tinaco::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', '=', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        return response()->json([
            'totalTinacos' => $totalTinacos,
            'tinacosByMonth' => $tinacosByMonth,
        ]);
    }
    public function obtenerRol()
    {
        $roles = ["Guest", "user", "Admin"];
        return response()->json([
            'roles' => $roles
        ], 200);
    }
}
