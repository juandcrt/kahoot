<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respuesta_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_juego_id')->constrained('sala_juegos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pregunta_id')->constrained('preguntas')->onDelete('cascade');
            $table->foreignId('opcion_id')->nullable()->constrained('opcions')->onDelete('cascade');
            $table->boolean('es_correcta')->default(false);
            $table->integer('tiempo_respuesta_segundos')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuesta_estudiantes');
    }
};