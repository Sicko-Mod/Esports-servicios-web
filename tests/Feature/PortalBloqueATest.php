<?php

namespace Tests\Feature;

use App\Enums\EstadoAsistencia;
use App\Models\Juego;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalBloqueATest extends TestCase
{
    use RefreshDatabase;

    public function test_registro_rechaza_correo_no_institucional(): void
    {
        $this->post('/portal/registro', [
            'name' => 'Externo',
            'email' => 'alguien@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_flujo_capitan_registra_asistencia_y_miembro_ve_porcentaje(): void
    {
        $semestre = Semestre::create([
            'nombre' => 'Prueba',
            'fecha_inicio' => now()->subMonth(),
            'fecha_fin' => now()->addMonths(3),
            'activo' => true,
        ]);

        $juego = Juego::create([
            'nombre' => 'Valorant',
            'slug' => 'valorant',
            'activo' => true,
        ]);

        $capitan = User::factory()->create(['email' => 'capitan@uamv.edu.ni']);
        $miembroUser = User::factory()->create(['email' => 'miembro@uamv.edu.ni']);

        $capitan->miembro()->create([
            'juego_id' => $juego->id,
            'gamer_tag' => 'Cap_1',
            'cif' => 'CIF001',
            'carrera' => 'Sistemas',
            'telefono' => '88887777',
            'perfil_completado_at' => now(),
        ]);

        $miembro = $miembroUser->miembro()->create([
            'juego_id' => $juego->id,
            'gamer_tag' => 'Player_1',
            'cif' => 'CIF002',
            'carrera' => 'Sistemas',
            'telefono' => '88886666',
            'perfil_completado_at' => now(),
        ]);

        $juego->update(['capitan_user_id' => $capitan->id, 'capitan' => 'Cap_1']);

        $this->actingAs($capitan)
            ->post('/portal/capitan/sesiones', [
                'juego_id' => $juego->id,
                'fecha' => now()->toDateString(),
            ])
            ->assertRedirect();

        $sesionId = $juego->sesiones()->first()->id;

        $this->actingAs($capitan)
            ->put('/portal/capitan/sesiones/'.$sesionId.'/lista', [
                'asistencias' => [
                    $miembro->id => EstadoAsistencia::Presente->value,
                ],
            ])
            ->assertRedirect(route('portal.capitan.sesiones.index'));

        $this->actingAs($miembroUser)
            ->get('/portal/mi-asistencia')
            ->assertOk()
            ->assertSee('100%');
    }

    public function test_login_del_portal_bloquea_tras_cinco_intentos_por_minuto(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/portal/login', ['email' => 'x@uamv.edu.ni', 'password' => 'mala'])
                ->assertRedirect();
        }

        $this->post('/portal/login', ['email' => 'x@uamv.edu.ni', 'password' => 'mala'])
            ->assertStatus(429);
    }

    public function test_login_del_admin_bloquea_tras_cinco_intentos_por_minuto(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'x@uamv.edu.ni', 'password' => 'mala'])
                ->assertRedirect();
        }

        $this->post('/admin/login', ['email' => 'x@uamv.edu.ni', 'password' => 'mala'])
            ->assertStatus(429);
    }

    public function test_el_bloqueo_de_un_correo_no_afecta_a_otro_desde_la_misma_ip(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->post('/portal/login', ['email' => 'x@uamv.edu.ni', 'password' => 'mala']);
        }

        $this->post('/portal/login', ['email' => 'otro@uamv.edu.ni', 'password' => 'mala'])
            ->assertRedirect();
    }

    public function test_grupo_inactivo_no_se_muestra_en_detalle_publico(): void
    {
        Juego::create(['nombre' => 'Oculto', 'slug' => 'oculto', 'activo' => false]);

        $this->get('/equipos/oculto')->assertNotFound();
    }
}
