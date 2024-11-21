<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Usuario;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'id_usuario',
        'type',
        'title',
        'message',
        'is_read',
    ];
     
    public function user()
    {
        return $this->belongsTo(Usuario::class);
    }

}
