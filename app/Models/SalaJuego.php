<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaJuego extends Model
{
    use HasFactory;

    protected $fillable = ['cuestionario_id', 'pin', 'estado'];

    public function cuestionario()
    {
        return $this->belongsTo(Cuestionario::class);
    }

    // Relación principal con los estudiantes conectados a la sala
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'sala_user', 'sala_juego_id', 'user_id')
                    ->withPivot('skin')
                    ->withTimestamps();
    }

    // Alias para que funcione tanto $sala->usuarios como $sala->alumnos
    public function alumnos()
    {
        return $this->usuarios();
    }
}