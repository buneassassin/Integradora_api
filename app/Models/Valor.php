<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class Valor extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'Valor';

    protected $fillable = [
        'sensor_id',
        'tinaco_id',
        'valor'
    ]; 

    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'sensor_id');
    }

    public function tinaco()
    {
        return $this->belongsTo(Tinaco::class, 'tinaco_id');
    }
}