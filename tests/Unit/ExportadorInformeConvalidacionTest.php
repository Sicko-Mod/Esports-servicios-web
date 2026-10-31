<?php

namespace Tests\Unit;

use App\Enums\EstadoSolicitudConvalidacion;
use App\Models\Miembro;
use App\Models\Semestre;
use App\Models\SolicitudConvalidacion;
use App\Models\User;
use App\Support\ExportadorInformeConvalidacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportadorInformeConvalidacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_resumen_suma_horas_solo_aprobadas_como_otorgadas(): void
    {
        $semestre = Semestre::create([
            'nombre' => 'S',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-12-01',
            'activo' => true,
        ]);

        $user = User::factory()->create();
        $m1 = Miembro::create(['user_id' => $user->id, 'gamer_tag' => 'A', 'perfil_completado_at' => now()]);
        $m2 = Miembro::create(['user_id' => User::factory()->create()->id, 'gamer_tag' => 'B', 'perfil_completado_at' => now()]);

        $solicitudes = collect([
            SolicitudConvalidacion::create([
                'miembro_id' => $m1->id,
                'semestre_id' => $semestre->id,
                'estado' => EstadoSolicitudConvalidacion::Aprobada,
                'porcentaje_asistencia' => 90,
                'sesiones_presentes' => 9,
                'sesiones_total' => 10,
                'horas_equivalentes' => 25,
                'enviada_at' => now(),
            ]),
            SolicitudConvalidacion::create([
                'miembro_id' => $m2->id,
                'semestre_id' => $semestre->id,
                'estado' => EstadoSolicitudConvalidacion::Rechazada,
                'porcentaje_asistencia' => 50,
                'sesiones_presentes' => 5,
                'sesiones_total' => 10,
                'horas_equivalentes' => 25,
                'enviada_at' => now(),
            ]),
        ]);

        $resumen = ExportadorInformeConvalidacion::resumen($solicitudes);

        $this->assertSame(2, $resumen['total']);
        $this->assertSame(1, $resumen['aprobadas']);
        $this->assertSame(1, $resumen['rechazadas']);
        $this->assertSame(25, $resumen['horas_aprobadas']);
        $this->assertSame(25, $resumen['horas_rechazadas_no_otorgadas']);
    }
}
