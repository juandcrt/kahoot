<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentFeedback extends Model {
    protected $fillable = ['sala_juego_id', 'user_id', 'feedback_text', 'status'];
}