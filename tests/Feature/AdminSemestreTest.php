<?php

namespace Tests\Feature;

use App\Models\Semestre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSemestreTest extends TestCase
{
    use RefreshDatabase;

    public function test_solo_un_semestre_puede_quedar_activo(): void
    {
        $admin = User::factory()->admin()->create();

        $viejo = Semestre::create([
            'nombre' => 'Anterior',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-06-30',
            'activo' => true,
        ]);

        $this->actingAs($admin)
            ->post('/admin/semestres', [
                'nombre' => 'Nuevo',
                'fecha_inicio' => '2026-08-01',
                'fecha_fin' => '2026-12-11',
                'activo' => '1',
            ])
            ->assertRedirect(route('admin.semestres.index'));

        $this->assertFalse($viejo->fresh()->activo);
        $this->assertTrue(Semestre::where('nombre', 'Nuevo')->first()->activo);
    }
}
