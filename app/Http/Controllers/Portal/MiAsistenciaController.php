<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Semestre;
use App\Support\CalculadoraAsistencia;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MiAsistenciaController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $miembro = $request->user()->miembro;
        $semestre = Semestre::actual();

        if (! $semestre || ! $miembro?->juego_id) {
            return redirect()
                ->route('portal.dashboard')
                ->with('estado', 'Todavía no hay semestre activo o no tenés grupo asignado.');
        }

        $resumen = CalculadoraAsistencia::paraMiembro($miembro, $semestre);

        $historial = $miembro->asistencias()
            ->with('sesion')
            ->whereHas('sesion', fn ($q) => $q->where('semestre_id', $semestre->id))
            ->get()
            ->sortByDesc(fn ($a) => $a->sesion->fecha);

        return view('portal.asistencia.index', compact('miembro', 'semestre', 'resumen', 'historial'));
    }
}
