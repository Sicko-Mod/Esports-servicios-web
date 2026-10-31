<?php

namespace App\Http\Controllers\Portal\Capitan;

use App\Enums\EstadoAsistencia;
use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Sesion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ListaAsistenciaController extends Controller
{
    public function edit(Request $request, Sesion $sesion): View
    {
        $this->autorizarCapitan($request, $sesion);

        $sesion->load('juego.miembros.user');
        $miembros = $sesion->juego->miembros()->orderBy('gamer_tag')->get();
        $marcas = $sesion->asistencias()->get()->keyBy('miembro_id');

        return view('portal.capitan.lista.edit', [
            'sesion' => $sesion,
            'miembros' => $miembros,
            'marcas' => $marcas,
            'estados' => EstadoAsistencia::cases(),
        ]);
    }

    public function update(Request $request, Sesion $sesion): RedirectResponse
    {
        $this->autorizarCapitan($request, $sesion);

        $miembrosIds = $sesion->juego->miembros()->pluck('id')->all();

        $datos = $request->validate([
            'asistencias' => ['required', 'array'],
            'asistencias.*' => ['required', Rule::enum(EstadoAsistencia::class)],
        ]);

        foreach ($datos['asistencias'] as $miembroId => $estado) {
            if (! in_array((int) $miembroId, $miembrosIds, true)) {
                continue;
            }

            Asistencia::updateOrCreate(
                ['sesion_id' => $sesion->id, 'miembro_id' => $miembroId],
                [
                    'estado' => $estado,
                    'marcado_por' => $request->user()->id,
                ]
            );
        }

        return redirect()
            ->route('portal.capitan.sesiones.index')
            ->with('estado', 'Lista guardada.');
    }

    private function autorizarCapitan(Request $request, Sesion $sesion): void
    {
        abort_unless($request->user()->esCapitanDe($sesion->juego), 403);
    }
}
