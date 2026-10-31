<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_id')->constrained('sesiones')->cascadeOnDelete();
            $table->foreignId('miembro_id')->constrained('miembros')->cascadeOnDelete();
            $table->string('estado', 20);
            $table->foreignId('marcado_por')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['sesion_id', 'miembro_id']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE asistencias ADD CONSTRAINT asistencias_estado_check CHECK (estado IN ('presente','ausente','justificado'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
