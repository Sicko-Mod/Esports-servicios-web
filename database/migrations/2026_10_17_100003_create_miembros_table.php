<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('miembros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('juego_id')->nullable()->constrained('juegos')->nullOnDelete();
            $table->string('gamer_tag')->nullable();
            $table->text('cif')->nullable();
            $table->string('carrera')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('tutor_nombre')->nullable();
            $table->string('tutor_telefono', 30)->nullable();
            $table->timestamp('tutor_consentimiento_at')->nullable();
            $table->timestamp('perfil_completado_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('miembros');
    }
};
