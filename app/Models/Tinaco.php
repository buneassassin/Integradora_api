<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tinaco extends Model
{
    use HasFactory;

    protected $table = 'tinaco';
    protected $fillable = [
        'id_usuario',
        'name',
        'nivel_del_agua',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function sensores()
    {
        return $this->hasMany(SensorTinaco::class, 'tinaco_id');
    }

    
}
