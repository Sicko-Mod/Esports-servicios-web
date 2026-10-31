<?php

namespace Tests\Feature;

use App\Enums\EstadoAsistencia;
use App\Enums\EstadoSolicitudConvalidacion;
use App\Models\Asistencia;
use App\Models\Juego;
use App\Models\Miembro;
use App\Models\Semestre;
use App\Models\Sesion;
use App\Models\SolicitudConvalidacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConvalidacionBloqueBTest extends TestCase
{
    use RefreshDatabase;

    public function test_miembro_elegible_puede_enviar_solicitud(): void
    {
        $semestre = Semestre::create([
            'nombre' => 'S1',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-12-01',
            'activo' => true,
        ]);

        $juego = Juego::create(['nombre' => 'V', 'slug' => 'v', 'activo' => true]);
        $user = User::factory()->create(['email' => 'm@uamv.edu.ni']);
        $capitan = User::factory()->create();

        $miembro = Miembro::create([
            'user_id' => $user->id,
            'juego_id' => $juego->id,
            'gamer_tag' => 'Tag',
            'cif' => 'CIF-99',
            'carrera' => 'Ing',
            'telefono' => '8888',
            'perfil_completado_at' => now(),
        ]);

        $sesion = Sesion::create([
            'juego_id' => $juego->id,
            'semestre_id' => $semestre->id,
            'fecha' => '2026-03-01',
            'registrado_por' => $capitan->id,
        ]);

        Asistencia::create([
            'sesion_id' => $sesion->id,
            'miembro_id' => $miembro->id,
            'estado' => EstadoAsistencia::Presente,
            'marcado_por' => $capitan->id,
        ]);

        $this->actingAs($user)
            ->post('/portal/convalidacion')
            ->assertRedirect(route('portal.convalidacion.index'));

        $this->assertDatabaseHas('solicitudes_convalidacion', [
            'miembro_id' => $miembro->id,
            'semestre_id' => $semestre->id,
            'estado' => EstadoSolicitudConvalidacion::Enviada->value,
            'porcentaje_asistencia' => 100,
        ]);
    }

    public function test_admin_exporta_csv_sin_nombre(): void
    {
        $admin = User::factory()->admin()->create();
        $semestre = Semestre::create([
            'nombre' => 'Export',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-12-01',
            'activo' => true,
        ]);

        $user = User::factory()->create(['name' => 'Nombre Secreto']);
        $miembro = Miembro::create([
            'user_id' => $user->id,
            'gamer_tag' => 'X',
            'cif' => 'CIF-EXPORT',
            'perfil_completado_at' => now(),
        ]);

        SolicitudConvalidacion::create([
            'miembro_id' => $miembro->id,
            'semestre_id' => $semestre->id,
            'estado' => EstadoSolicitudConvalidacion::Aprobada,
            'porcentaje_asistencia' => 90,
            'sesiones_presentes' => 9,
            'sesiones_total' => 10,
            'horas_equivalentes' => 25,
            'enviada_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/convalidaciones/exportar?semestre_id='.$semestre->id.'&tipo=informe');

        $response->assertOk();
        $response->assertHeader('content-disposition');

        $contenido = $response->streamedContent();
        $this->assertStringContainsString('INFORME DE CONVALIDACIONES', $contenido);
        $this->assertStringContainsString('horas_laborales_otorgadas_aprobadas', $contenido);
        $this->assertStringContainsString('DETALLE_TODAS_LAS_SOLICITUDES', $contenido);
        $this->assertStringContainsString('CIF-EXPORT', $contenido);
        $this->assertStringContainsString('aprobada', $contenido);
        $this->assertStringNotContainsString('Nombre Secreto', $contenido);
    }

    public function test_informe_incluye_aprobadas_y_rechazadas(): void
    {
        $admin = User::factory()->admin()->create();
        $semestre = Semestre::create([
            'nombre' => 'Mix',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-12-01',
            'activo' => true,
        ]);

        foreach ([EstadoSolicitudConvalidacion::Aprobada, EstadoSolicitudConvalidacion::Rechazada] as $i => $estado) {
            $miembro = Miembro::create([
                'user_id' => User::factory()->create()->id,
                'gamer_tag' => 'T'.$i,
                'cif' => 'CIF-'.$i,
                'perfil_completado_at' => now(),
            ]);

            SolicitudConvalidacion::create([
                'miembro_id' => $miembro->id,
                'semestre_id' => $semestre->id,
                'estado' => $estado,
                'porcentaje_asistencia' => 80,
                'sesiones_presentes' => 8,
                'sesiones_total' => 10,
                'horas_equivalentes' => 25,
                'enviada_at' => now(),
            ]);
        }

        $contenido = $this->actingAs($admin)
            ->get('/admin/convalidaciones/exportar?semestre_id='.$semestre->id.'&tipo=informe')
            ->streamedContent();

        $this->assertStringContainsString('solicitudes_aprobadas', $contenido);
        $this->assertStringContainsString('solicitudes_rechazadas', $contenido);
        $this->assertStringContainsString('CIF-0', $contenido);
        $this->assertStringContainsString('CIF-1', $contenido);
        $this->assertStringContainsString('rechazada', $contenido);
    }
}
