<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoSolicitudConvalidacion;
use App\Http\Controllers\Controller;
use App\Models\Semestre;
use App\Models\SolicitudConvalidacion;
use App\Support\ExportadorInformeConvalidacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SolicitudConvalidacionController extends Controller
{
    public function index(Request $request): View
    {
        $semestreId = $request->integer('semestre_id') ?: null;
        $estado = $request->string('estado')->toString() ?: null;

        $consulta = SolicitudConvalidacion::query()
            ->with(['miembro.juego', 'miembro.user', 'semestre'])
            ->orderByDesc('enviada_at');

        if ($semestreId) {
            $consulta->where('semestre_id', $semestreId);
        }

        if ($estado) {
            $consulta->where('estado', $estado);
        }

        $resumenInforme = null;
        if ($semestreId) {
            $todasDelSemestre = SolicitudConvalidacion::query()
                ->where('semestre_id', $semestreId)
                ->get();
            $resumenInforme = ExportadorInformeConvalidacion::resumen($todasDelSemestre);
        }

        return view('admin.convalidaciones.index', [
            'solicitudes' => $consulta->get(),
            'semestres' => Semestre::query()->orderByDesc('fecha_inicio')->get(),
            'semestreId' => $semestreId,
            'estadoFiltro' => $estado,
            'estados' => EstadoSolicitudConvalidacion::cases(),
            'resumenInforme' => $resumenInforme,
        ]);
    }

    public function edit(SolicitudConvalidacion $convalidacion): View
    {
        $convalidacion->load(['miembro.user', 'miembro.juego', 'semestre', 'revisadoPor']);

        return view('admin.convalidaciones.edit', [
            'solicitud' => $convalidacion,
            'estados' => EstadoSolicitudConvalidacion::cases(),
        ]);
    }

    public function update(Request $request, SolicitudConvalidacion $convalidacion): RedirectResponse
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::enum(EstadoSolicitudConvalidacion::class)],
            'notas_admin' => ['nullable', 'string', 'max:4000'],
        ]);

        $convalidacion->fill([
            'estado' => $datos['estado'],
            'notas_admin' => $datos['notas_admin'] ?? null,
            'revisado_por' => $request->user()->id,
            'revisada_at' => now(),
        ]);
        $convalidacion->save();

        return redirect()
            ->route('admin.convalidaciones.index', ['semestre_id' => $convalidacion->semestre_id])
            ->with('estado', 'Solicitud actualizada.');
    }

    public function exportar(Request $request): StreamedResponse
    {
        $datos = $request->validate([
            'semestre_id' => ['required', 'exists:semestres,id'],
            'tipo' => ['nullable', Rule::in(['informe', 'registro'])],
        ]);

        $semestre = Semestre::findOrFail($datos['semestre_id']);
        $tipo = $datos['tipo'] ?? 'informe';

        $solicitudes = SolicitudConvalidacion::query()
            ->with(['miembro.juego'])
            ->where('semestre_id', $semestre->id)
            ->orderBy('id')
            ->get();

        if ($tipo === 'registro') {
            $solicitudes = $solicitudes->filter(
                fn (SolicitudConvalidacion $s) => $s->estado === EstadoSolicitudConvalidacion::Aprobada
            );

            $nombreArchivo = 'registro-aprobados-'.str($semestre->nombre)->slug().'-'.now()->format('Y-m-d').'.csv';
        } else {
            $nombreArchivo = 'informe-convalidaciones-'.str($semestre->nombre)->slug().'-'.now()->format('Y-m-d').'.csv';
        }

        return response()->streamDownload(function () use ($semestre, $solicitudes, $tipo) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");

            if ($tipo === 'registro') {
                ExportadorInformeConvalidacion::escribirRegistroAprobadas($salida, $semestre, $solicitudes);
            } else {
                ExportadorInformeConvalidacion::escribirInformeCompleto($salida, $semestre, $solicitudes);
            }

            fclose($salida);
        }, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
