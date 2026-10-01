<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model {
    protected $fillable = ['sala_juego_id', 'pregunta_id', 'user_id', 'opcion_id', 'is_correct', 'time_ms', 'points'];

    public function user() { return $this->belongsTo(User::class); }
    public function pregunta() { return $this->belongsTo(Pregunta::class); }
    public function opcion() { return $this->belongsTo(Opcion::class); }
}