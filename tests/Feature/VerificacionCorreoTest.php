<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerificacionCorreoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_registro_envia_un_enlace_de_confirmacion(): void
    {
        Notification::fake();

        $this->post('/portal/registro', [
            'name' => 'Nuevo Miembro',
            'email' => 'nuevo@uamv.edu.ni',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('portal.verification.notice'));

        $user = User::where('email', 'nuevo@uamv.edu.ni')->firstOrFail();

        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_sin_confirmar_no_puede_cargar_su_perfil(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'sin@uamv.edu.ni']);

        $this->actingAs($user)
            ->get('/portal/perfil')
            ->assertRedirect(route('portal.verification.notice'));
    }

    public function test_el_enlace_firmado_confirma_el_correo(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'link@uamv.edu.ni']);

        $url = URL::temporarySignedRoute('portal.verification.verify', now()->addHour(), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect(route('portal.perfil.edit'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_el_enlace_de_otro_usuario_no_confirma(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'a@uamv.edu.ni']);
        $otro = User::factory()->unverified()->create(['email' => 'b@uamv.edu.ni']);

        $url = URL::temporarySignedRoute('portal.verification.verify', now()->addHour(), [
            'id' => $otro->id,
            'hash' => sha1($otro->email),
        ]);

        $this->actingAs($user)->get($url)->assertForbidden();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
