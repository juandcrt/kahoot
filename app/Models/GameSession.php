<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GameSession extends Model {
    protected $fillable = ['pin', 'status', 'current_question_id'];
    public function scores() { return $this->hasMany(PlayerScore::class); }
}