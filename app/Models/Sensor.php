<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    protected $table = 'sensor';
    protected $fillable = [ 
        'nombre',
        'modelo',
        'unidad_medida',
        'rango_min',
        'rango_max'
    ];

    public function valor()
    {
        return $this->hasMany(Valor::class, 'sensor_id');
    }
}
