<?php

namespace Tests\Feature;

use App\Enums\EstadoAsistencia;
use App\Models\Asistencia;
use App\Models\Juego;
use App\Models\Miembro;
use App\Models\Semestre;
use App\Models\Sesion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReporteAsistenciaTest extends TestCase
{
    use RefreshDatabase;

    private Semestre $semestre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->semestre = Semestre::create([
            'nombre' => 'Reporte',
            'fecha_inicio' => '2026-08-01',
            'fecha_fin' => '2026-12-11',
            'activo' => true,
        ]);

        $juego = Juego::create(['nombre' => 'Overwatch', 'slug' => 'overwatch', 'activo' => true]);
        $capitan = User::factory()->create();
        $miembro = Miembro::create([
            'user_id' => User::factory()->create(['name' => 'Jugador Reporte'])->id,
            'juego_id' => $juego->id,
            'gamer_tag' => 'Tag_Reporte',
            'perfil_completado_at' => now(),
        ]);

        foreach ([['2026-09-01', EstadoAsistencia::Presente], ['2026-09-08', EstadoAsistencia::Ausente]] as [$fecha, $estado]) {
            $sesion = Sesion::create([
                'juego_id' => $juego->id,
                'semestre_id' => $this->semestre->id,
                'fecha' => $fecha,
                'registrado_por' => $capitan->id,
            ]);

            Asistencia::create([
                'sesion_id' => $sesion->id,
                'miembro_id' => $miembro->id,
                'estado' => $estado,
                'marcado_por' => $capitan->id,
            ]);
        }
    }

    public function test_el_admin_ve_el_resumen_por_grupo(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/asistencia?semestre_id='.$this->semestre->id)
            ->assertOk()
            ->assertSee('Overwatch')
            ->assertSee('Tag_Reporte')
            ->assertSee('50%');
    }

    public function test_el_csv_trae_una_fila_por_miembro_y_sesion(): void
    {
        $contenido = $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/asistencia/exportar?semestre_id='.$this->semestre->id)
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('fecha_sesion', $contenido);
        $this->assertStringContainsString('2026-09-01,Tag_Reporte,"Jugador Reporte"', $contenido);
        $this->assertStringContainsString('presente', $contenido);
        $this->assertStringContainsString('ausente', $contenido);
    }

    public function test_un_miembro_no_ve_el_reporte(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/asistencia')
            ->assertForbidden();
    }
}
