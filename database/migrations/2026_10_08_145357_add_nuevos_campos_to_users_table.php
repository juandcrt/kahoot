<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregamos los nuevos campos permitiendo que sean nulos temporalmente
            $table->string('apellidos')->nullable()->after('name');
            $table->string('dni', 15)->nullable()->unique()->after('apellidos');
            $table->string('grado_seccion')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // En caso de querer revertir, eliminamos los campos
            $table->dropColumn(['apellidos', 'dni', 'grado_seccion']);
        });
    }
};