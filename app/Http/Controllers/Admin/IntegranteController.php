<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Nivel;
use App\Http\Controllers\Controller;
use App\Models\Integrante;
use App\Models\Juego;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IntegranteController extends Controller
{
    public function index(Request $request): View
    {
        $juegoId = $request->integer('juego_id') ?: null;

        $consulta = Integrante::query()->with('juego')->orderBy('gamer_tag');

        if ($juegoId) {
            $consulta->where('juego_id', $juegoId);
        }

        return view('admin.integrantes.index', [
            'integrantes' => $consulta->get(),
            'juegos' => Juego::query()->ordenados()->get(),
            'juegoId' => $juegoId,
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.integrantes.create', [
            'integrante' => new Integrante,
            'juegos' => Juego::query()->ordenados()->get(),
            'juegoId' => $request->integer('juego_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        Integrante::create($datos);

        return redirect()
            ->route('admin.integrantes.index', ['juego_id' => $datos['juego_id']])
            ->with('estado', 'Integrante agregado al roster.');
    }

    public function edit(Integrante $integrante): View
    {
        return view('admin.integrantes.edit', [
            'integrante' => $integrante,
            'juegos' => Juego::query()->ordenados()->get(),
        ]);
    }

    public function update(Request $request, Integrante $integrante): RedirectResponse
    {
        $datos = $this->validar($request, $integrante);

        $integrante->update($datos);

        return redirect()
            ->route('admin.integrantes.index', ['juego_id' => $integrante->juego_id])
            ->with('estado', 'Integrante actualizado.');
    }

    public function destroy(Integrante $integrante): RedirectResponse
    {
        $juegoId = $integrante->juego_id;
        $integrante->delete();

        return redirect()
            ->route('admin.integrantes.index', ['juego_id' => $juegoId])
            ->with('estado', 'Integrante eliminado del roster.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request, ?Integrante $integrante = null): array
    {
        $juegoId = $request->input('juego_id');

        return $request->validate([
            'juego_id' => ['required', 'exists:juegos,id'],
            'gamer_tag' => [
                'required',
                'string',
                'max:255',
                Rule::unique('integrantes', 'gamer_tag')
                    ->where(fn ($query) => $query->where('juego_id', $juegoId))
                    ->ignore($integrante),
            ],
            'nivel' => ['required', Rule::enum(Nivel::class)],
            'nombre_publico' => ['nullable', 'string', 'max:255'],
        ], [], [
            'juego_id' => 'grupo',
            'gamer_tag' => 'gamer tag',
            'nivel' => 'nivel',
            'nombre_publico' => 'nombre público',
        ]);
    }
}
