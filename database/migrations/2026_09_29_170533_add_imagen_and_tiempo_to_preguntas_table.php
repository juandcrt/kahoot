<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preguntas', function (Blueprint $table) {
            $table->string('imagen')->nullable()->after('pregunta');
            $table->integer('tiempo')->default(20)->after('imagen'); // Tiempo en segundos (por defecto 20s)
        });
    }

    public function down(): void
    {
        Schema::table('preguntas', function (Blueprint $table) {
            $table->dropColumn(['imagen', 'tiempo']);
        });
    }
};