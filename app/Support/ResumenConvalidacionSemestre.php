<?php

namespace App\Support;

use App\Enums\EstadoSolicitudConvalidacion;
use App\Models\Semestre;
use App\Models\SolicitudConvalidacion;
use Illuminate\Support\Collection;

final class ResumenConvalidacionSemestre
{
    /**
     * @return array{
     *     semestre: Semestre,
     *     solicitudes: Collection<int, SolicitudConvalidacion>,
     *     total: int,
     *     por_estado: array<string, int>,
     *     horas_aprobadas: int,
     *     horas_en_tramite: int,
     *     horas_rechazadas: int,
     *     por_grupo: list<array{grupo: string, total: int, aprobadas: int, rechazadas: int, horas_aprobadas: int}>
     * }
     */
    public static function paraSemestre(Semestre $semestre): array
    {
        $solicitudes = SolicitudConvalidacion::query()
            ->with(['miembro.juego'])
            ->where('semestre_id', $semestre->id)
            ->orderBy('id')
            ->get();

        $porEstado = [];
        foreach (EstadoSolicitudConvalidacion::cases() as $estado) {
            $porEstado[$estado->value] = $solicitudes->where('estado', $estado)->count();
        }

        $horasAprobadas = (int) $solicitudes
            ->where('estado', EstadoSolicitudConvalidacion::Aprobada)
            ->sum('horas_equivalentes');

        $horasEnTramite = (int) $solicitudes
            ->whereIn('estado', [
                EstadoSolicitudConvalidacion::Enviada,
                EstadoSolicitudConvalidacion::EnRevision,
            ])
            ->sum('horas_equivalentes');

        $horasRechazadas = (int) $solicitudes
            ->where('estado', EstadoSolicitudConvalidacion::Rechazada)
            ->sum('horas_equivalentes');

        $porGrupo = $solicitudes
            ->groupBy(fn (SolicitudConvalidacion $s) => $s->miembro->juego?->nombre ?? 'Sin grupo')
            ->map(function (Collection $grupo, string $nombre) {
                return [
                    'grupo' => $nombre,
                    'total' => $grupo->count(),
                    'aprobadas' => $grupo->where('estado', EstadoSolicitudConvalidacion::Aprobada)->count(),
                    'rechazadas' => $grupo->where('estado', EstadoSolicitudConvalidacion::Rechazada)->count(),
                    'horas_aprobadas' => (int) $grupo
                        ->where('estado', EstadoSolicitudConvalidacion::Aprobada)
                        ->sum('horas_equivalentes'),
                ];
            })
            ->values()
            ->all();

        return [
            'semestre' => $semestre,
            'solicitudes' => $solicitudes,
            'total' => $solicitudes->count(),
            'por_estado' => $porEstado,
            'horas_aprobadas' => $horasAprobadas,
            'horas_en_tramite' => $horasEnTramite,
            'horas_rechazadas' => $horasRechazadas,
            'por_grupo' => $porGrupo,
        ];
    }
}
