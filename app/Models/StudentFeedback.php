<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentFeedback extends Model {
    
    // ESTA ES LA LÍNEA QUE TE FALTABA PARA QUE LARAVEL LEA LA "S"
    protected $table = 'student_feedbacks';
    
    protected $fillable = ['sala_juego_id', 'user_id', 'feedback_text', 'status'];
}