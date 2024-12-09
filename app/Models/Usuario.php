<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Notification;


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
        'is_Inactive',
        'remember_token',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function tinacos()
    {
        return $this->hasMany(Tinaco::class, 'id_usuario');
    }
    
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
