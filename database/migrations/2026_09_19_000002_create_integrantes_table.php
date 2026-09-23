<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integrantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('juego_id')->constrained('juegos')->cascadeOnDelete();
            $table->string('gamer_tag');
            $table->string('nivel', 10)->default('miembro');
            $table->string('nombre_publico')->nullable();   // solo con permiso expreso; vacío si es menor
            $table->timestamps();

            $table->unique(['juego_id', 'gamer_tag']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE integrantes ADD CONSTRAINT integrantes_nivel_check CHECK (nivel IN ('tier-1','tier-2','miembro'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('integrantes');
    }
};
