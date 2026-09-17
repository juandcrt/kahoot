<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PlayerScore extends Model {
    protected $fillable = ['game_session_id', 'nickname', 'score'];
}