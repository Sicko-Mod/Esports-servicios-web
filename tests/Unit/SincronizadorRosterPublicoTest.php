<?php

namespace Tests\Unit;

use App\Enums\Nivel;
use App\Models\Integrante;
use App\Models\Juego;
use App\Models\Miembro;
use App\Models\User;
use App\Support\SincronizadorRosterPublico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SincronizadorRosterPublicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_integrante_publico_al_completar_perfil(): void
    {
        $juego = Juego::create(['nombre' => 'Valorant', 'slug' => 'valorant', 'activo' => true]);
        $user = User::factory()->create();

        $miembro = Miembro::create([
            'user_id' => $user->id,
            'juego_id' => $juego->id,
            'gamer_tag' => 'Sync_Tag',
            'cif' => 'ENC',
            'carrera' => 'Ing',
            'telefono' => '8888',
            'perfil_completado_at' => now(),
        ]);

        SincronizadorRosterPublico::sincronizar($miembro);

        $this->assertDatabaseHas('integrantes', [
            'miembro_id' => $miembro->id,
            'juego_id' => $juego->id,
            'gamer_tag' => 'Sync_Tag',
            'nivel' => Nivel::Miembro->value,
        ]);

        $this->get('/equipos/valorant')->assertSee('Sync_Tag');
    }

    public function test_conserva_tier_si_ya_existia_vinculo(): void
    {
        $juego = Juego::create(['nombre' => 'OW', 'slug' => 'ow', 'activo' => true]);
        $user = User::factory()->create();

        $miembro = Miembro::create([
            'user_id' => $user->id,
            'juego_id' => $juego->id,
            'gamer_tag' => 'Pro',
            'perfil_completado_at' => now(),
        ]);

        Integrante::create([
            'miembro_id' => $miembro->id,
            'juego_id' => $juego->id,
            'gamer_tag' => 'Pro',
            'nivel' => Nivel::Tier1,
        ]);

        $miembro->update(['gamer_tag' => 'Pro_V2']);
        SincronizadorRosterPublico::sincronizar($miembro->fresh());

        $this->assertDatabaseHas('integrantes', [
            'miembro_id' => $miembro->id,
            'gamer_tag' => 'Pro_V2',
            'nivel' => Nivel::Tier1->value,
        ]);
    }
}
