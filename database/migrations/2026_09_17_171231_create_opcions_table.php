<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('opcions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregunta_id')->constrained()->onDelete('cascade');
            $table->string('opcion');
            $table->boolean('es_correcta')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('opcions'); }
};