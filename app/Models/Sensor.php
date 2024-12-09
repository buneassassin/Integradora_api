<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    protected $table = 'sensor';
    protected $fillable = [
        'id_rango',
        'nombre',
        'modelo',
        'unidad_medida',
    ];

    public function sensorTinacos()
    {
        return $this->hasMany(SensorTinaco::class, 'sensor_id');
    }

    public function rango()
    {
        return $this->belongsTo(Rango::class, 'id_rango');
    }
}
