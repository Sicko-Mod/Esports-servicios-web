<?php

namespace Database\Seeders;

use App\Models\Semestre;
use Illuminate\Database\Seeder;

class SemestreSeeder extends Seeder
{
    public function run(): void
    {
        Semestre::updateOrCreate(
            ['nombre' => 'Segundo semestre 2026'],
            [
                'fecha_inicio' => '2026-08-01',
                'fecha_fin' => '2026-12-11',
                'minimo_sesiones' => null,
                'activo' => true,
            ]
        );
    }
}
