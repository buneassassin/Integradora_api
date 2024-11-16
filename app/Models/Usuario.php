<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;


// paquete para guardar imagenes ejemplo:
/*
 $image = Image::make('path/to/image.jpg');
$image->resize(300, 200);
$image->save('path/to/image.jpg');
 */
use Intervention\Image\ImageManagerStatic as Image;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $table = 'usuario';

    protected $fillable = [
        'id_persona',
        'usuario_nom',
        'email',
        'email_verified_at',
        'foto_perfil',
        'password',
        'is_active',
        'remember_token',
    ];

    public function persona()
    {
        return $this->hasOne(Persona::class, 'id_usuario');
    }

    public function tinacos()
    {
        return $this->hasMany(Tinaco::class, 'id_usuario');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}


// cosa a agregar al register de autenticadorController:
/*
public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'usuario_nom' => 'required',
        'email' => 'required|email|unique:usuario',
        'password' => 'required|min:6'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => $validator->errors()
        ], 400);
    }

    // Crear el usuario
    $user = Usuario::create([
        'usuario_nom' => $request->usuario_nom,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role_id' => 1, // Asignar el role_id del rol 'guest' (por ejemplo, el ID 1)
    ]);

    // Asignar el rol con Spatie (opcional)
    $user->assignRole('guest'); // Asumiendo que el rol 'guest' está definido

    $url = URL::temporarySignedRoute('activate', now()->addMinutes(5), ['user' => $user->id]);

    Mail::to($user->email)->send(new Activacion($user, $url));

    return response()->json([
        'message' => 'Usuario creado exitosamente, revisa tu correo para activarlo.'
    ], 201);
}

*/