<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Juego;
use App\Models\Torneo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TorneoController extends Controller
{
    public function index(): View
    {
        return view('admin.torneos.index', [
            'torneos' => Torneo::query()->with('juego')->orderByDesc('fecha_inicio')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.torneos.create', [
            'torneo' => new Torneo,
            'juegos' => Juego::query()->ordenados()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Torneo::create($this->validar($request));

        return redirect()
            ->route('admin.torneos.index')
            ->with('estado', 'Torneo creado.');
    }

    public function edit(Torneo $torneo): View
    {
        return view('admin.torneos.edit', [
            'torneo' => $torneo,
            'juegos' => Juego::query()->ordenados()->get(),
        ]);
    }

    public function update(Request $request, Torneo $torneo): RedirectResponse
    {
        $torneo->update($this->validar($request));

        return redirect()
            ->route('admin.torneos.index')
            ->with('estado', 'Torneo actualizado.');
    }

    public function destroy(Torneo $torneo): RedirectResponse
    {
        $torneo->delete();

        return redirect()
            ->route('admin.torneos.index')
            ->with('estado', 'Torneo eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request): array
    {
        if (! $request->filled('juego_id')) {
            $request->merge(['juego_id' => null]);
        }

        $datos = $request->validate([
            'juego_id' => ['nullable', 'exists:juegos,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'modalidad' => ['required', Rule::in(['individual', 'equipo'])],
            'abierto_externos' => ['sometimes', 'boolean'],
            'enlace_inscripcion' => ['nullable', 'url', 'max:2048'],
        ], [], [
            'juego_id' => 'grupo',
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'modalidad' => 'modalidad',
            'abierto_externos' => 'abierto a externos',
            'enlace_inscripcion' => 'enlace de inscripción',
        ]);

        $datos['abierto_externos'] = $request->boolean('abierto_externos');
        $datos['juego_id'] = $datos['juego_id'] ?? null;

        return $datos;
    }
}
