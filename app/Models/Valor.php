<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Valor extends Model
{
    use HasFactory;

    protected $table = 'valor';
    protected $fillable = [
        'id_sensor',
        'id_rango',
        'value',
    ];

    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'id_sensor');
    }

    public function rango()
    {
        return $this->belongsTo(Rango::class, 'id_rango');
    }
}
