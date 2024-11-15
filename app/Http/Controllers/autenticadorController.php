<?php

namespace App\Http\Controllers;

use App\Mail\Activacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;


class autenticadorController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_nom' => 'required',
            'email' => 'required|email|unique:usuario',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }

        $user = new Usuario();

        $user->usuario_nom = $request->usuario_nom;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->rol = 'guest';
        $user->save();


        $url = URL::temporarySignedRoute('activate', now()->addMinutes(1), ['user' => $user->id]);

        Mail::to($user->email)->send(new Activacion($user, $url));

        return response()->json([
            'message' => 'Se ha creado el usuario, fijate en el email crack'
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

        $user->rol = 'usuario';      

        $user->save();

        return response()->json([
            'message' => 'Account activated successfully',
        ], 200);
    }

    //agregar esto a activate

    /*
    necesito: id del usuario,
    email,
    nombre
    */

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
