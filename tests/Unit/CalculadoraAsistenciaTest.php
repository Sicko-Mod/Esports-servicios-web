<?php

namespace Tests\Unit;

use App\Enums\EstadoAsistencia;
use App\Models\Asistencia;
use App\Models\Juego;
use App\Models\Miembro;
use App\Models\Semestre;
use App\Models\Sesion;
use App\Models\User;
use App\Support\CalculadoraAsistencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculadoraAsistenciaTest extends TestCase
{
    use RefreshDatabase;

    public function test_calcula_porcentaje_sobre_sesiones_del_grupo(): void
    {
        $semestre = Semestre::create([
            'nombre' => 'S1',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-06-01',
            'activo' => true,
        ]);

        $juego = Juego::create(['nombre' => 'OW', 'slug' => 'ow']);
        $user = User::factory()->create();
        $miembro = Miembro::create([
            'user_id' => $user->id,
            'juego_id' => $juego->id,
            'gamer_tag' => 'X',
            'perfil_completado_at' => now(),
        ]);

        $sesion1 = Sesion::create([
            'juego_id' => $juego->id,
            'semestre_id' => $semestre->id,
            'fecha' => '2026-02-01',
            'registrado_por' => $user->id,
        ]);
        $sesion2 = Sesion::create([
            'juego_id' => $juego->id,
            'semestre_id' => $semestre->id,
            'fecha' => '2026-02-08',
            'registrado_por' => $user->id,
        ]);

        Asistencia::create([
            'sesion_id' => $sesion1->id,
            'miembro_id' => $miembro->id,
            'estado' => EstadoAsistencia::Presente,
            'marcado_por' => $user->id,
        ]);
        Asistencia::create([
            'sesion_id' => $sesion2->id,
            'miembro_id' => $miembro->id,
            'estado' => EstadoAsistencia::Ausente,
            'marcado_por' => $user->id,
        ]);

        $resumen = CalculadoraAsistencia::paraMiembro($miembro, $semestre);

        $this->assertSame(1, $resumen['presentes']);
        $this->assertSame(2, $resumen['total_sesiones']);
        $this->assertSame(50.0, $resumen['porcentaje']);
    }

    public function test_los_justificados_cuentan_como_ausencia_por_defecto(): void
    {
        $this->assertSame(50.0, CalculadoraAsistencia::porcentaje(presentes: 1, justificados: 1, totalSesiones: 2));
    }

    public function test_los_justificados_se_descuentan_del_total_si_el_club_lo_activa(): void
    {
        config(['club.justificados_descuentan_del_total' => true]);

        $this->assertSame(100.0, CalculadoraAsistencia::porcentaje(presentes: 1, justificados: 1, totalSesiones: 2));
        $this->assertSame(0.0, CalculadoraAsistencia::porcentaje(presentes: 0, justificados: 2, totalSesiones: 2));
    }
}
