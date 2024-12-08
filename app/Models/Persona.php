<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'persona';
    protected $fillable = [
        'nombres',
        'a_p',
        'a_m',
        'telefono',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_persona');
    }
    
}
