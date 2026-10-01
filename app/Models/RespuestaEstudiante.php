<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaEstudiante extends Model
{
    use HasFactory;

    // Esto le da permiso a Laravel para guardar los datos
    protected $fillable = [
        'sala_juego_id',
        'user_id',
        'pregunta_id',
        'opcion_id',
        'es_correcta',
        'tiempo_respuesta_segundos'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}