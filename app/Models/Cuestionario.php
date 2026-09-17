<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuestionario extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'titulo', 'tipo'];

    // Definir la relación con el usuario (profesor creador)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Opcional: Relación con las preguntas del cuestionario
    public function preguntas()
    {
        return $this->hasMany(Pregunta::class);
    }
}