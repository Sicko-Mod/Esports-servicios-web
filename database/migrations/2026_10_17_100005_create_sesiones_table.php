<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('juego_id')->constrained('juegos')->cascadeOnDelete();
            $table->foreignId('semestre_id')->constrained('semestres')->cascadeOnDelete();
            $table->date('fecha');
            $table->foreignId('registrado_por')->constrained('users')->cascadeOnDelete();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->unique(['juego_id', 'semestre_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones');
    }
};
