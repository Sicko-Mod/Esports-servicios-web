<?php

namespace Tests\Feature;

use App\Models\Integrante;
use App\Models\Juego;
use App\Models\Torneo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitioPublicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_las_paginas_publicas_responden(): void
    {
        $rutas = [
            '/', '/equipos', '/torneos', '/convalidacion-de-creditos',
            '/como-unirse', '/contacto', '/politica-de-privacidad',
        ];

        foreach ($rutas as $ruta) {
            $this->get($ruta)->assertOk();
        }
    }

    public function test_un_grupo_inactivo_no_aparece_en_equipos(): void
    {
        Juego::create(['nombre' => 'Valorant', 'slug' => 'valorant']);

        $this->get('/equipos')
            ->assertOk()
            ->assertSee('Todavía no hay grupos activos')
            ->assertDontSee('Valorant');
    }

    public function test_un_grupo_activo_aparece_con_su_horario(): void
    {
        Juego::create([
            'nombre' => 'Valorant',
            'slug' => 'valorant',
            'activo' => true,
            'dia' => 'martes',
            'hora' => '18:00',
            'lugar' => 'Laboratorio 3',
            'capitan' => 'Demo_Alfa',
        ]);

        $this->get('/equipos')
            ->assertOk()
            ->assertSee('Valorant')
            ->assertSee('Martes 6:00 pm')
            ->assertSee('Laboratorio 3')
            ->assertSee('Demo_Alfa');
    }

    public function test_el_roster_se_agrupa_por_nivel(): void
    {
        $juego = Juego::create(['nombre' => 'Valorant', 'slug' => 'valorant', 'activo' => true]);

        Integrante::create(['juego_id' => $juego->id, 'gamer_tag' => 'Demo_Miembro', 'nivel' => 'miembro']);
        Integrante::create(['juego_id' => $juego->id, 'gamer_tag' => 'Demo_Titular', 'nivel' => 'tier-1']);

        $this->get('/equipos/valorant')
            ->assertOk()
            ->assertSeeInOrder(['Tier 1', 'Demo_Titular', 'Miembros', 'Demo_Miembro'])
            ->assertDontSee('Tier 2');
    }

    public function test_un_juego_que_no_existe_da_404(): void
    {
        $this->get('/equipos/no-existe')->assertNotFound();
    }

    public function test_los_torneos_se_separan_en_proximos_e_historial(): void
    {
        Torneo::create(['nombre' => 'Copa Futura', 'fecha_inicio' => now()->addDays(10)]);
        Torneo::create(['nombre' => 'Copa Vieja', 'fecha_inicio' => now()->subDays(10)]);

        $this->get('/torneos')
            ->assertOk()
            ->assertSeeInOrder(['Copa Futura', 'Historial', 'Copa Vieja']);
    }
}
