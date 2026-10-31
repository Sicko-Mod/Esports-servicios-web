<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Juego;
use App\Models\Miembro;
use App\Support\SincronizadorRosterPublico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerfilController extends Controller
{
    public function edit(Request $request): View
    {
        $miembro = $this->miembroDe($request);

        return view('portal.perfil.edit', [
            'miembro' => $miembro,
            'juegos' => Juego::activos()->ordenados()->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $miembro = $this->miembroDe($request);

        $datos = $request->validate([
            'gamer_tag' => ['required', 'string', 'max:255'],
            'juego_id' => ['required', 'exists:juegos,id'],
            'cif' => ['required', 'string', 'max:32'],
            'carrera' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:30'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'tutor_nombre' => ['nullable', 'string', 'max:255'],
            'tutor_telefono' => ['nullable', 'string', 'max:30'],
            'tutor_consentimiento' => ['sometimes', 'accepted'],
        ], [], [
            'gamer_tag' => 'gamer tag',
            'juego_id' => 'grupo',
            'cif' => 'CIF',
            'carrera' => 'carrera',
            'telefono' => 'teléfono',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'tutor_nombre' => 'nombre del tutor',
            'tutor_telefono' => 'teléfono del tutor',
            'tutor_consentimiento' => 'consentimiento del tutor',
        ]);

        $miembro->fill([
            'gamer_tag' => $datos['gamer_tag'],
            'juego_id' => $datos['juego_id'],
            'cif' => $datos['cif'],
            'carrera' => $datos['carrera'],
            'telefono' => $datos['telefono'],
            'fecha_nacimiento' => $datos['fecha_nacimiento'],
            'tutor_nombre' => $datos['tutor_nombre'] ?? null,
            'tutor_telefono' => $datos['tutor_telefono'] ?? null,
        ]);

        if ($miembro->esMenor()) {
            $request->validate([
                'tutor_nombre' => ['required', 'string', 'max:255'],
                'tutor_telefono' => ['required', 'string', 'max:30'],
                'tutor_consentimiento' => ['accepted'],
            ], [], [
                'tutor_nombre' => 'nombre del tutor',
                'tutor_telefono' => 'teléfono del tutor',
                'tutor_consentimiento' => 'consentimiento del tutor',
            ]);

            $miembro->tutor_consentimiento_at = now();
        } else {
            $miembro->tutor_nombre = null;
            $miembro->tutor_telefono = null;
            $miembro->tutor_consentimiento_at = null;
        }

        $miembro->perfil_completado_at = now();
        $miembro->save();

        SincronizadorRosterPublico::sincronizar($miembro->fresh());

        $juego = Juego::find($datos['juego_id']);
        if ($juego && $juego->capitan_user_id === $request->user()->id) {
            $juego->update(['capitan' => $datos['gamer_tag']]);
        }

        return redirect()
            ->route('portal.dashboard')
            ->with('estado', 'Perfil guardado.');
    }

    private function miembroDe(Request $request): Miembro
    {
        $user = $request->user();

        return $user->miembro ?? $user->miembro()->create([]);
    }
}
