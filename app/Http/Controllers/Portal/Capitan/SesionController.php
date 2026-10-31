<?php

namespace App\Http\Controllers\Portal\Capitan;

use App\Http\Controllers\Controller;
use App\Models\Semestre;
use App\Models\Sesion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SesionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $grupos = $user->juegosComoCapitan()->activos()->ordenados()->get();
        $semestre = Semestre::actual();

        $sesiones = $semestre
            ? Sesion::query()
                ->with('juego')
                ->where('semestre_id', $semestre->id)
                ->whereIn('juego_id', $grupos->pluck('id'))
                ->orderByDesc('fecha')
                ->limit(20)
                ->get()
            : collect();

        return view('portal.capitan.sesiones.index', compact('grupos', 'semestre', 'sesiones'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $semestre = Semestre::actual();

        abort_unless($semestre, 404);

        $datos = $request->validate([
            'juego_id' => ['required', 'exists:juegos,id'],
            'fecha' => ['required', 'date'],
            'notas' => ['nullable', 'string', 'max:2000'],
        ]);

        $juego = $user->juegosComoCapitan()->whereKey($datos['juego_id'])->first();
        abort_unless($juego, 403);

        $sesion = Sesion::firstOrCreate(
            [
                'juego_id' => $juego->id,
                'semestre_id' => $semestre->id,
                'fecha' => $datos['fecha'],
            ],
            [
                'registrado_por' => $user->id,
                'notas' => $datos['notas'] ?? null,
            ]
        );

        return redirect()
            ->route('portal.capitan.lista.edit', $sesion)
            ->with('estado', $sesion->wasRecentlyCreated ? 'Sesión creada. Marcá la asistencia.' : 'Sesión ya existía; podés actualizar la lista.');
    }
}
