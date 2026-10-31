<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semestre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SemestreController extends Controller
{
    public function index(): View
    {
        return view('admin.semestres.index', [
            'semestres' => Semestre::query()->orderByDesc('fecha_inicio')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.semestres.create', ['semestre' => new Semestre]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        DB::transaction(function () use ($datos) {
            if ($datos['activo']) {
                Semestre::query()->update(['activo' => false]);
            }

            Semestre::create($datos);
        });

        return redirect()
            ->route('admin.semestres.index')
            ->with('estado', 'Semestre creado.');
    }

    public function edit(Semestre $semestre): View
    {
        return view('admin.semestres.edit', compact('semestre'));
    }

    public function update(Request $request, Semestre $semestre): RedirectResponse
    {
        $datos = $this->validar($request);

        DB::transaction(function () use ($datos, $semestre) {
            if ($datos['activo']) {
                Semestre::query()->whereKeyNot($semestre->id)->update(['activo' => false]);
            }

            $semestre->update($datos);
        });

        return redirect()
            ->route('admin.semestres.index')
            ->with('estado', 'Semestre actualizado.');
    }

    public function destroy(Semestre $semestre): RedirectResponse
    {
        if ($semestre->sesiones()->exists()) {
            return back()->withErrors([
                'semestre' => 'No se puede eliminar: ya tiene sesiones de asistencia registradas.',
            ]);
        }

        $semestre->delete();

        return redirect()
            ->route('admin.semestres.index')
            ->with('estado', 'Semestre eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request): array
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'minimo_sesiones' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'activo' => ['sometimes', 'boolean'],
        ], [], [
            'nombre' => 'nombre',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'minimo_sesiones' => 'mínimo de sesiones',
            'activo' => 'semestre activo',
        ]);

        $datos['activo'] = $request->boolean('activo');
        $datos['minimo_sesiones'] = $request->filled('minimo_sesiones')
            ? (int) $request->input('minimo_sesiones')
            : null;

        return $datos;
    }
}
