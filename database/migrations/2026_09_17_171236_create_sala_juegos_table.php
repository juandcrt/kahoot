<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sala_juegos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuestionario_id')->constrained()->onDelete('cascade');
            $table->string('pin', 6)->unique();
            $table->string('estado')->default('activa');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('sala_juegos'); }
};