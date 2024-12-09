<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Valor extends Model
{
    use HasFactory;

    protected $table = 'valor';
    protected $fillable = [
        'id_rango',
        'value',
    ];

    
}
