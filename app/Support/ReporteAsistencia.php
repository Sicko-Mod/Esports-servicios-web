<?php

namespace App\Support;

use App\Enums\EstadoAsistencia;
use App\Models\Asistencia;
use App\Models\Juego;
use App\Models\Miembro;
use App\Models\Semestre;
use Illuminate\Support\Collection;

final class ReporteAsistencia
{
    /**
     * Uses the group's current roster; members who moved to another group
     * mid-semester appear under their new group.
     *
     * @return array{sesiones: Collection, filas: Collection}
     */
    public static function paraGrupo(Juego $juego, Semestre $semestre): array
    {
        $sesiones = $juego->sesiones()
            ->where('semestre_id', $semestre->id)
            ->orderBy('fecha')
            ->get();

        $marcasPorMiembro = Asistencia::query()
            ->whereIn('sesion_id', $sesiones->pluck('id'))
            ->get()
            ->groupBy('miembro_id');

        $total = $sesiones->count();

        $filas = $juego->miembros()
            ->with('user')
            ->orderBy('gamer_tag')
            ->get()
            ->map(function (Miembro $miembro) use ($marcasPorMiembro, $total) {
                $marcas = ($marcasPorMiembro[$miembro->id] ?? collect())
                    ->mapWithKeys(fn (Asistencia $a) => [$a->sesion_id => $a->estado]);

                $contar = fn (EstadoAsistencia $estado) => $marcas->filter(fn ($e) => $e === $estado)->count();
                $presentes = $contar(EstadoAsistencia::Presente);
                $justificados = $contar(EstadoAsistencia::Justificado);
                $porcentaje = CalculadoraAsistencia::porcentaje($presentes, $justificados, $total);

                return [
                    'miembro' => $miembro,
                    'presentes' => $presentes,
                    'ausentes' => $contar(EstadoAsistencia::Ausente),
                    'justificados' => $justificados,
                    'sin_marca' => $total - $marcas->count(),
                    'porcentaje' => $porcentaje,
                    'cumple_umbral' => $total > 0 && CalculadoraAsistencia::cumpleUmbral($porcentaje),
                    'marcas' => $marcas,
                ];
            });

        return ['sesiones' => $sesiones, 'filas' => $filas];
    }
}
