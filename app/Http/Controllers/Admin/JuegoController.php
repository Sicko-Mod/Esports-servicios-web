<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Juego;
use App\Models\User;
use App\Support\UniqueSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JuegoController extends Controller
{
    public function index(): View
    {
        return view('admin.juegos.index', [
            'juegos' => Juego::query()->ordenados()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.juegos.create', ['juego' => new Juego]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);
        $datos['slug'] = UniqueSlug::for(Juego::class, $datos['nombre']);

        Juego::create($datos);

        return redirect()
            ->route('admin.juegos.index')
            ->with('estado', 'Grupo creado.');
    }

    public function edit(Juego $juego): View
    {
        return view('admin.juegos.edit', [
            'juego' => $juego,
            'capitanesCandidatos' => $this->capitanesCandidatos($juego),
        ]);
    }

    public function update(Request $request, Juego $juego): RedirectResponse
    {
        $datos = $this->validar($request);
        $datos['slug'] = UniqueSlug::for(Juego::class, $datos['nombre'], $juego->id);

        $juego->update($datos);

        return redirect()
            ->route('admin.juegos.index')
            ->with('estado', 'Grupo actualizado.');
    }

    public function destroy(Juego $juego): RedirectResponse
    {
        $juego->delete();

        return redirect()
            ->route('admin.juegos.index')
            ->with('estado', 'Grupo eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request): array
    {
        if (! $request->filled('hora')) {
            $request->merge(['hora' => null]);
        }

        if (! $request->filled('dia')) {
            $request->merge(['dia' => null]);
        }

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'dia' => ['nullable', 'in:'.implode(',', array_keys(Juego::DIAS))],
            'hora' => ['nullable', 'date_format:H:i'],
            'lugar' => ['nullable', 'string', 'max:255'],
            'capitan' => ['nullable', 'string', 'max:255'],
            'capitan_user_id' => ['nullable', 'exists:users,id'],
            'orden' => ['integer', 'min:0', 'max:65535'],
        ], [], [
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'dia' => 'día',
            'hora' => 'hora',
            'lugar' => 'lugar',
            'capitan' => 'capitán',
            'orden' => 'orden',
        ]);

        if (! $request->filled('capitan_user_id')) {
            $datos['capitan_user_id'] = null;
        }

        $datos['activo'] = $request->boolean('activo');
        $datos['orden'] = (int) $request->input('orden', 0);

        return $datos;
    }

    /**
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function capitanesCandidatos(Juego $juego)
    {
        return User::query()
            ->whereHas('miembro', fn ($q) => $q->where('juego_id', $juego->id))
            ->orderBy('name')
            ->get();
    }
}
