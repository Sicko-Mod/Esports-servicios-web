<?php

namespace Tests\Feature;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsuariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_admin_ve_la_lista_de_usuarios(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['name' => 'Miembro Visible']);

        $this->actingAs($admin)
            ->get('/admin/usuarios')
            ->assertOk()
            ->assertSee('Miembro Visible');
    }

    public function test_un_superadmin_puede_cambiar_el_rol_de_otro_usuario(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $miembro = User::factory()->create();

        $this->actingAs($superadmin)
            ->put('/admin/usuarios/'.$miembro->id, ['rol' => Rol::Superadmin->value])
            ->assertRedirect(route('admin.usuarios.index'));

        $this->assertSame(Rol::Superadmin, $miembro->fresh()->rol);
    }

    public function test_un_admin_comun_no_puede_cambiar_roles(): void
    {
        $admin = User::factory()->admin()->create();
        $miembro = User::factory()->create();

        $this->actingAs($admin)
            ->put('/admin/usuarios/'.$miembro->id, ['rol' => Rol::Admin->value])
            ->assertForbidden();

        $this->assertSame(Rol::Miembro, $miembro->fresh()->rol);
    }

    public function test_no_se_puede_quitar_el_ultimo_superadmin(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->put('/admin/usuarios/'.$superadmin->id, ['rol' => Rol::Miembro->value])
            ->assertSessionHasErrors('rol');

        $this->assertSame(Rol::Superadmin, $superadmin->fresh()->rol);
    }

    public function test_avisa_cuando_falta_el_segundo_superadmin(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->get('/admin/usuarios')
            ->assertSee('Hoy hay 1');
    }
}
