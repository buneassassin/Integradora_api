<?php

namespace App\Http\Controllers;

use App\Mail\Activacion;
use App\Mail\ResetPassword;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Tinaco;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;


class autenticadorController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_nom'      => 'nullable|string',
            'email'            => 'required|email|unique:usuario',
            'password'         => 'required',
            'nombres'          => 'nullable|string',
            'apellidoPaterno'  => 'nullable|string',
            'apellidoMaterno'  => 'nullable|string',
            'telefono'         => 'nullable|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }
    
        // Si no se envían ciertos datos, asignamos valores por defecto sutiles
        $persona = new Persona();
        $persona->nombres = $request->nombres ?? 'Sin nombre';
        $persona->a_p = $request->apellidoPaterno ?? 'Sin apellido paterno';
        $persona->a_m = $request->apellidoMaterno ?? 'Sin apellido materno';
        $persona->telefono = $request->telefono ?? '0000000000';
        $persona->save();
    
        $user = new Usuario();
        // Si no se proporciona usuario_nom, se utiliza el nombre o el valor por defecto 'Sin nombre'
        $user->usuario_nom = $request->usuario_nom ?? ($request->nombres ?? 'Sin nombre');
        $user->id_persona = $persona->id;
        $user->email = $request->email;
        $user->foto_perfil = "https://ui-avatars.com/api/?name=" . urlencode($user->usuario_nom) . "&color=7F9CF5&background=EBF4FF";
        $user->password = bcrypt($request->password);
        $user->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente'
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }

        $user = Usuario::where('email', $request->email)->first();

        // vereficar si el usuario existe
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        // vereficar si el usuario esta activo
        if (!$user->is_Inactive) {
            return response()->json([
                'message' => 'Usuario esta desactivado'
            ], 401);
        }
        // vereficar si el password es correcto
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $mesaje = '¡Hola ' . $user->usuario_nom . '! Bienvenido a la plataforma';
        $notificacion = new Notification();
        $notificacion->title = 'Bienvenido';
        $notificacion->message = $mesaje;
        $notificacion->id_usuario = $user->id;
        $notificacion->type = 'info';
        $notificacion->is_read = false;
        $notificacion->save();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $user->createToken($user->email)->plainTextToken
        ], 200);
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_nom' => 'sometimes|string|max:50',
            'nombres' => 'sometimes|string|max:50',
            'apellidoPaterno' => 'sometimes|string|max:50',
            'apellidoMaterno' => 'sometimes|string|max:50',
            'telefono' => 'sometimes|string|max:10|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }

        $user = $request->user();

        // Actualizar solo los campos que estén presentes en la solicitud
        if ($request->filled('usuario_nom')) {
            $user->usuario_nom = $request->usuario_nom;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        $user->save();

        $persona = Persona::find($user->id_persona);

        if ($request->filled('nombres')) {
            $persona->nombres = $request->nombres;
        }
        if ($request->filled('apellidoPaterno')) {
            $persona->a_p = $request->apellidoPaterno;
        }
        if ($request->filled('apellidoMaterno')) {
            $persona->a_m = $request->apellidoMaterno;
        }
        if ($request->filled('telefono')) {
            $persona->telefono = $request->telefono;
        }
        $persona->save();

        return response()->json(['message' => 'Usuario actualizado correctamente.'], 200);
    }
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
            'password_new' => 'required|string|min:8|',
            'password_confirmation' => 'required|string|min:8|same:password_new',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }
        //validar si el password es correcto
        if (!password_verify($request->password, $request->user()->password)) {
            return response()->json([
                'message' => 'Contraseña incorrecta'

            ], 400);
        }

        //validar si los passwords son iguales
        if ($request->password_new != $request->password_confirmation) {
            return response()->json([
                'message' => 'Contraseñas no coinciden'
            ], 400);
        }

        $user = $request->user();
        $user->password = bcrypt($request->password_new);
        $user->save();

        return response()->json(['message' => 'Password updated successfully.'], 200);
    }
    public function recuperarPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }
        $user = Usuario::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Email not found'
            ], 404);
        }
        $url = URL::temporarySignedRoute('reset-password', now()->addMinutes(5), ['user' => $user->id]);
        $activarCuenta = new ResetPassword($user, $url);

        Mail::to($user->email)->send($activarCuenta);

        return response()->json([
            'message' => 'Correo enviado correctamente, revisa tu correo',
            'email' => $user->email,
            'url' => $url
        ], 200);
    }
    public function showResetForm($userId)
    {
        // Verifica si el enlace es válido
        $user = Usuario::findOrFail($userId);
        return view('auth.reset_password_form', ['user' => $user]);
    }
    public function resetPassword(Request $request, $userId)
    {
        $user = Usuario::findOrFail($userId);
        $user->password = bcrypt($request->password);
        $user->save();
    
        return redirect()->back()->with('success', '¡Contraseña cambiada correctamente!');
    }
    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();


        return response()->json(['message' => 'Sesión cerrada correctamente.'], 200);
    }
    public function me(Request $request)
    {
        // Obtener el usuario autenticado con los datos de `persona`
        $user = $request->user()->load('persona');

        return response()->json([
            'success' => true,
            'message' => 'User info retrieved successfully',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'rol' => $user->rol,
                'is_Inactive' => $user->is_Inactive,
                'is_active' => $user->is_active,
                'id_persona' => $user->id_persona,
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
    public function activate($userId)
    {
        $user = Usuario::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if ($user->is_active) {
            return response()->json([
                'message' => 'Account already activated'
            ], 200);
        }

        $user->is_active = true;
        $user->rol = 'user';

        $user->save();

        return response()->json([
            'message' => 'Account activated successfully',
        ], 200);
    }
    public function resendActivation(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'usuario_nom' => 'required',
            'email' => 'required|email|unique:usuario',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = Usuario::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $url = URL::temporarySignedRoute('activate', now()->addMinutes(5), ['user' => $user->id]);

        $activarCuenta = new Activacion($user, $url);

        Mail::to($user->email)->send($activarCuenta);

        return response()->json([
            'message' => 'bueno, no se te vuelva a olvidar lo del correo bro',
        ]);
    }
    //revia de correo de activacion
    public function sendEmail(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = Usuario::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        //vereficar si el correo ya esta activad
        if ($user->is_active) {
            return response()->json([
                'message' => 'Account already activated'
            ], 200);
        }

        $url = URL::temporarySignedRoute('activate', now()->addMinutes(5), ['user' => $user->id]);

        $activarCuenta = new Activacion($user, $url);

        Mail::to($user->email)->send($activarCuenta);

        return response()->json([
            'message' => 'bueno, no se te vuelva a olvidar lo del correo bro',
        ]);
    }
}
