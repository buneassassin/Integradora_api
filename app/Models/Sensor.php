<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    protected $table = 'sensor';
    protected $fillable = [
        'id_valor',
        'id_tinaco',
        'nombre',
        'modelo',
        'unidad_medida',
    ];

    public function valores()
    {
        return $this->hasMany(Valor::class, 'id_sensor');
    }

    public function rango()
    {
        return $this->belongsTo(Rango::class, 'id_valor');
    }
}
