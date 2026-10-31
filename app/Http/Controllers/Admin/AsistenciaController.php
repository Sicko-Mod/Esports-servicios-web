<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Juego;
use App\Models\Semestre;
use App\Support\ReporteAsistencia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AsistenciaController extends Controller
{
    public function index(Request $request): View
    {
        $semestre = $this->semestreElegido($request);
        $juegoId = $request->integer('juego_id') ?: null;

        $reportes = $semestre
            ? $this->juegos($juegoId)->map(fn (Juego $juego) => [
                'juego' => $juego,
                ...ReporteAsistencia::paraGrupo($juego, $semestre),
            ])
            : collect();

        return view('admin.asistencia.index', [
            'semestres' => Semestre::query()->orderByDesc('fecha_inicio')->get(),
            'juegos' => Juego::query()->orderBy('nombre')->get(),
            'semestre' => $semestre,
            'juegoId' => $juegoId,
            'reportes' => $reportes,
        ]);
    }

    public function exportar(Request $request): StreamedResponse
    {
        $datos = $request->validate([
            'semestre_id' => ['required', 'exists:semestres,id'],
            'juego_id' => ['nullable', 'exists:juegos,id'],
        ]);

        $semestre = Semestre::findOrFail($datos['semestre_id']);
        $juegos = $this->juegos(isset($datos['juego_id']) ? (int) $datos['juego_id'] : null);
        $nombreArchivo = 'asistencia-'.str($semestre->nombre)->slug().'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($semestre, $juegos) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, ['semestre', 'grupo', 'fecha_sesion', 'gamer_tag', 'nombre', 'correo', 'estado']);

            foreach ($juegos as $juego) {
                $reporte = ReporteAsistencia::paraGrupo($juego, $semestre);

                foreach ($reporte['sesiones'] as $sesion) {
                    foreach ($reporte['filas'] as $fila) {
                        fputcsv($salida, [
                            $semestre->nombre,
                            $juego->nombre,
                            $sesion->fecha->format('Y-m-d'),
                            $fila['miembro']->gamer_tag,
                            $fila['miembro']->user?->name,
                            $fila['miembro']->user?->email,
                            $fila['marcas']->get($sesion->id)?->value ?? 'sin_marca',
                        ]);
                    }
                }
            }

            fclose($salida);
        }, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function semestreElegido(Request $request): ?Semestre
    {
        $semestreId = $request->integer('semestre_id');

        return $semestreId ? Semestre::find($semestreId) : Semestre::actual();
    }

    /**
     * @return Collection<int, Juego>
     */
    private function juegos(?int $juegoId): Collection
    {
        return Juego::query()
            ->when($juegoId, fn ($consulta) => $consulta->whereKey($juegoId))
            ->orderBy('nombre')
            ->get();
    }
}
