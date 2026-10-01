<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('respuesta_estudiantes', function (Blueprint $table) {
            $table->unsignedBigInteger('sala_juego_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('pregunta_id')->nullable();
            $table->unsignedBigInteger('opcion_id')->nullable();
            $table->boolean('es_correcta')->default(false);
            $table->float('tiempo_respuesta_segundos')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('respuesta_estudiantes', function (Blueprint $table) {
            $table->dropColumn([
                'sala_juego_id', 
                'user_id', 
                'pregunta_id', 
                'opcion_id', 
                'es_correcta', 
                'tiempo_respuesta_segundos'
            ]);
        });
    }
};