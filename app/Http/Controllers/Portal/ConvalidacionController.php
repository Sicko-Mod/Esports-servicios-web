<?php

namespace App\Http\Controllers\Portal;

use App\Enums\EstadoSolicitudConvalidacion;
use App\Http\Controllers\Controller;
use App\Models\Semestre;
use App\Models\SolicitudConvalidacion;
use App\Support\ElegibilidadConvalidacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConvalidacionController extends Controller
{
    public function index(Request $request): View
    {
        $miembro = $request->user()->miembro;
        $semestre = Semestre::actual();
        $evaluacion = ElegibilidadConvalidacion::evaluar($miembro, $semestre);

        $historial = SolicitudConvalidacion::query()
            ->with('semestre')
            ->where('miembro_id', $miembro->id)
            ->orderByDesc('enviada_at')
            ->get();

        return view('portal.convalidacion.index', [
            'semestre' => $semestre,
            'evaluacion' => $evaluacion,
            'historial' => $historial,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $miembro = $request->user()->miembro;
        $semestre = Semestre::actual();

        abort_unless($semestre, 404);

        $evaluacion = ElegibilidadConvalidacion::evaluar($miembro, $semestre);

        if (! $evaluacion['elegible']) {
            return back()->withErrors(['convalidacion' => $evaluacion['motivos'][0] ?? 'No podés enviar la solicitud.']);
        }

        $datos = $request->validate([
            'notas_miembro' => ['nullable', 'string', 'max:2000'],
        ]);

        $resumen = $evaluacion['resumen'];

        SolicitudConvalidacion::create([
            'miembro_id' => $miembro->id,
            'semestre_id' => $semestre->id,
            'estado' => EstadoSolicitudConvalidacion::Enviada,
            'porcentaje_asistencia' => (int) round($resumen['porcentaje']),
            'sesiones_presentes' => $resumen['presentes'],
            'sesiones_total' => $resumen['total_sesiones'],
            'horas_equivalentes' => (int) config('club.horas_por_semestre'),
            'notas_miembro' => $datos['notas_miembro'] ?? null,
            'enviada_at' => now(),
        ]);

        return redirect()
            ->route('portal.convalidacion.index')
            ->with('estado', 'Solicitud enviada. El club la revisará con Registro Académico.');
    }
}
