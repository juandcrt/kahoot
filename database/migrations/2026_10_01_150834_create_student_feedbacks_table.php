<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_juego_id')->constrained('sala_juegos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('feedback_text');
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('student_feedbacks'); }
};