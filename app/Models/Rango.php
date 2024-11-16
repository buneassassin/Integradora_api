<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rango extends Model
{
    use HasFactory;

    protected $table = 'rango';
    protected $fillable = [
        'rango_min',
        'rango_max',
    ];

    public function valores()
    {
        return $this->hasMany(Valor::class, 'id_rango');
    }
}
