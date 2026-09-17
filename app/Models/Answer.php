<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model {
    protected $fillable = ['game_session_id', 'question_id', 'option_id', 'player_score_id', 'is_correct', 'points_earned'];
}