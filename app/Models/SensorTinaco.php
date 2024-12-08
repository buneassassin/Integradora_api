<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorTinaco extends Model
{
    use HasFactory;

    protected $table = 'sensor_tinaco';

    protected $fillable = [
        'sensor_id',
        'tinaco_id',
        'id_valor',
    ];

    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'sensor_id');
    }

    public function tinaco()
    {
        return $this->belongsTo(Tinaco::class, 'tinaco_id');
    }

    public function valor()
    {
        return $this->hasOne(Valor::class, 'id', 'id_valor');
    }
}
