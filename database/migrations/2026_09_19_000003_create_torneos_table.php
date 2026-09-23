<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('torneos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('juego_id')->nullable()->constrained('juegos')->nullOnDelete();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio')->index();
            $table->date('fecha_fin')->nullable();
            $table->string('modalidad', 12)->default('individual');
            $table->boolean('abierto_externos')->default(false);
            $table->string('enlace_inscripcion')->nullable();
            $table->timestamps();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE torneos ADD CONSTRAINT torneos_modalidad_check CHECK (modalidad IN ('individual','equipo'))");
            DB::statement('ALTER TABLE torneos ADD CONSTRAINT torneos_fechas_check CHECK (fecha_fin IS NULL OR fecha_fin >= fecha_inicio)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('torneos');
    }
};
