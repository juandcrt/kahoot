<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_juego_id')->constrained('sala_juegos')->onDelete('cascade');
            $table->foreignId('pregunta_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('opcion_id')->nullable()->constrained('opcions')->onDelete('cascade');
            $table->boolean('is_correct')->default(false);
            $table->integer('time_ms')->default(0);
            $table->integer('points')->default(0);
            $table->timestamps();
            
            // Restricción única: Un alumno solo puede responder 1 vez a 1 pregunta en 1 sala
            $table->unique(['sala_juego_id', 'pregunta_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('respuestas'); }
};