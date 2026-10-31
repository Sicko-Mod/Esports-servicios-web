<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_convalidacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('miembro_id')->constrained('miembros')->cascadeOnDelete();
            $table->foreignId('semestre_id')->constrained('semestres')->cascadeOnDelete();
            $table->string('estado', 20);
            $table->unsignedTinyInteger('porcentaje_asistencia');
            $table->unsignedSmallInteger('sesiones_presentes');
            $table->unsignedSmallInteger('sesiones_total');
            $table->unsignedTinyInteger('horas_equivalentes');
            $table->text('notas_miembro')->nullable();
            $table->text('notas_admin')->nullable();
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('enviada_at');
            $table->timestamp('revisada_at')->nullable();
            $table->timestamps();

            $table->unique(['miembro_id', 'semestre_id']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE solicitudes_convalidacion ADD CONSTRAINT solicitudes_convalidacion_estado_check CHECK (estado IN ('enviada','en_revision','aprobada','rechazada'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_convalidacion');
    }
};
