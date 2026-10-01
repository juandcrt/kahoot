<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    use HasFactory;

    protected $fillable = [
        'cuestionario_id', 
        'pregunta', 
        'imagen', 
        'tiempo'
    ];

    /**
     * Relación con las opciones de respuesta de la pregunta.
     */
    public function opciones()
    {
        return $this->hasMany(Opcion::class);
    }
}