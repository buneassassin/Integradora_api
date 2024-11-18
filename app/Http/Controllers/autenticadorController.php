<?php

namespace App\Http\Controllers;

use App\Mail\Activacion;
use App\Mail\ResetPassword;
use App\Models\Usuario;
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;


class autenticadorController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_nom' => 'required',
            'email' => 'required|email|unique:usuario',
            'password' => 'required',
            'nombres' => 'required',
            'apellidoPaterno' => 'required',
            'apellidoMaterno' => 'required',
            'telefono' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }
        $persona = new Persona();

        $persona->nombres = $request->nombres;
        $persona->a_p = $request->apellidoPaterno;
        $persona->a_m = $request->apellidoMaterno;
        $persona->telefono = $request->telefono;
        $persona->save();
        $user = new Usuario();

        $user->usuario_nom = $request->usuario_nom;
        $user->id_persona = $persona->id;
        $user->email = $request->email;
        $user->foto_perfil = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y';
        $user->password = bcrypt($request->password);
        $user->save();


        $url = URL::temporarySignedRoute('activate', now()->addMinutes(1), ['user' => $user->id]);

        Mail::to($user->email)->send(new Activacion($user, $url));

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
        // vereficar si el usuario existe o el password es correcto
        if (!$user || !password_verify($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ])
                ->setStatusCode(401);
        }

        /*    if (!$user->is_active) {
            return response()->json([
                'message' => 'Account not activated'
            ], 401);
        }*/
       
        $mensaje='El usuario '.$user->usuario_nom.' ha iniciado sesion';

        //validamos si exito o no el SLACK_KEY
        if (env('SLACK_KEY')) {
            Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SLACK_KEY'),
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying() // Deshabilita la verificación SSL
            ->post('https://slack.com/api/chat.postMessage', [
                'channel' => '#informal',
                'text' => $mensaje,
            ]);
        }
    
      
      

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $user->createToken($user->email)->plainTextToken
        ], 200);
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_nom' => 'sometimes|string',
            'email' => 'sometimes|email|unique:usuario,email,' . $request->user()->id,
            'nombres' => 'sometimes|string',
            'apellidoPaterno' => 'sometimes|string',
            'apellidoMaterno' => 'sometimes|string',
            'telefono' => 'sometimes|string'
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
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }
        //validar si los passwords son iguales
        if ($request->password != $request->password_confirmation) {
            return response()->json([
                'message' => 'Contraseñas no coinciden'
            ], 400);
        }

        $user = $request->user();
        $user->password = bcrypt($request->password);
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
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Usuario::findOrFail($userId);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Contraseña cambiada correctamente!'
        ])->setStatusCode(200);

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
}
