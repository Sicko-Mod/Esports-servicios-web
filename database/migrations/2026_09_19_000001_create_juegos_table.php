<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('juegos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->string('dia', 12)->nullable();
            $table->time('hora')->nullable();
            $table->string('lugar')->nullable();
            $table->string('capitan')->nullable();          // gamer tag del capitán del semestre
            $table->boolean('activo')->default(false);      // «Grupo activo este semestre»
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE juegos ADD CONSTRAINT juegos_dia_check CHECK (dia IS NULL OR dia IN ('lunes','martes','miercoles','jueves','viernes','sabado','domingo'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('juegos');
    }
};
