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
}