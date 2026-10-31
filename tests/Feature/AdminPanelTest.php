<?php

namespace Tests\Feature;

use App\Models\Juego;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitado_redirige_al_login_desde_el_panel(): void
    {
        $this->get('/admin/juegos')->assertRedirect(route('admin.login'));
    }

    public function test_gamer_tag_con_comillas_no_rompe_el_confirm_del_roster(): void
    {
        $admin = User::factory()->admin()->create();
        $juego = Juego::create(['nombre' => 'Valorant', 'slug' => 'valorant']);
        $juego->integrantes()->create(['gamer_tag' => "x');alert(1);//", 'nivel' => 'miembro']);

        $this->actingAs($admin)
            ->get('/admin/integrantes')
            ->assertOk()
            ->assertDontSee("confirm('¿Quitar a x&#039;);alert(1);//", false)
            ->assertSee('\u0027', false);
    }

    public function test_usuario_sin_permiso_no_accede_al_panel(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->get('/admin/juegos')
            ->assertForbidden();
    }

    public function test_admin_puede_crear_y_publicar_un_grupo(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/admin/juegos', [
                'nombre' => 'Valorant',
                'dia' => 'martes',
                'hora' => '18:00',
                'lugar' => 'Lab 3',
                'capitan' => 'Cap_Demo',
                'activo' => '1',
                'orden' => '1',
            ])
            ->assertRedirect(route('admin.juegos.index'));

        $juego = Juego::where('nombre', 'Valorant')->first();

        $this->assertNotNull($juego);
        $this->assertTrue($juego->activo);
        $this->assertSame('valorant', $juego->slug);

        $this->get('/equipos')->assertSee('Valorant');
    }

    public function test_admin_puede_gestionar_roster_y_torneos(): void
    {
        $admin = User::factory()->admin()->create();
        $juego = Juego::create(['nombre' => 'Overwatch', 'slug' => 'overwatch', 'activo' => true]);

        $this->actingAs($admin)
            ->post('/admin/integrantes', [
                'juego_id' => $juego->id,
                'gamer_tag' => 'Demo_Player',
                'nivel' => 'tier-1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('integrantes', [
            'juego_id' => $juego->id,
            'gamer_tag' => 'Demo_Player',
            'nivel' => 'tier-1',
        ]);

        $this->actingAs($admin)
            ->post('/admin/torneos', [
                'nombre' => 'Copa Interna',
                'fecha_inicio' => now()->addWeek()->toDateString(),
                'modalidad' => 'equipo',
                'juego_id' => $juego->id,
            ])
            ->assertRedirect(route('admin.torneos.index'));

        $this->assertDatabaseHas('torneos', ['nombre' => 'Copa Interna']);

        $this->get('/equipos/overwatch')
            ->assertOk()
            ->assertSee('Demo_Player');

        $this->get('/torneos')->assertSee('Copa Interna');
    }

    public function test_login_rechaza_cuenta_sin_flag_admin(): void
    {
        User::factory()->create([
            'email' => 'editor@example.com',
            'password' => 'secret-password',
        ]);

        $this->post('/admin/login', [
            'email' => 'editor@example.com',
            'password' => 'secret-password',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_slug_unico_si_hay_nombre_duplicado(): void
    {
        $admin = User::factory()->admin()->create();
        Juego::create(['nombre' => 'Valorant', 'slug' => 'valorant']);

        $this->actingAs($admin)
            ->post('/admin/juegos', [
                'nombre' => 'Valorant',
                'orden' => '0',
            ])
            ->assertRedirect(route('admin.juegos.index'));

        $this->assertDatabaseHas('juegos', ['slug' => 'valorant-2']);
    }
}
