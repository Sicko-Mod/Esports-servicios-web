<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('semestres', function (Blueprint $table) {
            $table->boolean('solicitudes_abiertas')->default(true)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('semestres', function (Blueprint $table) {
            $table->dropColumn('solicitudes_abiertas');
        });
    }
};
