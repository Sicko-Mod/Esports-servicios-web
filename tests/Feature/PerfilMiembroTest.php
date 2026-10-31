<?php

namespace Tests\Feature;

use App\Models\Juego;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerfilMiembroTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_fecha_de_nacimiento_es_obligatoria(): void
    {
        [$user, $juego] = $this->miembroSinPerfil();

        $this->actingAs($user)
            ->put('/portal/perfil', $this->datos($juego, ['fecha_nacimiento' => '']))
            ->assertSessionHasErrors('fecha_nacimiento');

        $this->assertNull($user->fresh()->miembro->perfil_completado_at);
    }

    public function test_un_menor_sin_consentimiento_del_tutor_no_completa_el_perfil(): void
    {
        [$user, $juego] = $this->miembroSinPerfil();

        $this->actingAs($user)
            ->put('/portal/perfil', $this->datos($juego, [
                'fecha_nacimiento' => now()->subYears(16)->toDateString(),
            ]))
            ->assertSessionHasErrors(['tutor_nombre', 'tutor_telefono', 'tutor_consentimiento']);

        $this->assertNull($user->fresh()->miembro->perfil_completado_at);
    }

    public function test_un_menor_con_consentimiento_completa_el_perfil(): void
    {
        [$user, $juego] = $this->miembroSinPerfil();

        $this->actingAs($user)
            ->put('/portal/perfil', $this->datos($juego, [
                'fecha_nacimiento' => now()->subYears(16)->toDateString(),
                'tutor_nombre' => 'Tutor Demo',
                'tutor_telefono' => '88880000',
                'tutor_consentimiento' => '1',
            ]))
            ->assertRedirect(route('portal.dashboard'));

        $miembro = $user->fresh()->miembro;

        $this->assertNotNull($miembro->perfil_completado_at);
        $this->assertNotNull($miembro->tutor_consentimiento_at);
    }

    /**
     * @return array{0: User, 1: Juego}
     */
    private function miembroSinPerfil(): array
    {
        $juego = Juego::create(['nombre' => 'Valorant', 'slug' => 'valorant', 'activo' => true]);
        $user = User::factory()->create(['email' => 'perfil@uamv.edu.ni']);

        return [$user, $juego];
    }

    /**
     * @return array<string, mixed>
     */
    private function datos(Juego $juego, array $cambios = []): array
    {
        return array_merge([
            'gamer_tag' => 'Perfil_Demo',
            'juego_id' => $juego->id,
            'cif' => 'CIF-PERFIL',
            'carrera' => 'Sistemas',
            'telefono' => '88881111',
            'fecha_nacimiento' => now()->subYears(20)->toDateString(),
        ], $cambios);
    }
}
