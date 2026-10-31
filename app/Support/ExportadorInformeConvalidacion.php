<?php

namespace App\Support;

use App\Enums\EstadoSolicitudConvalidacion;
use App\Models\Semestre;
use App\Models\SolicitudConvalidacion;
use Illuminate\Support\Collection;

final class ExportadorInformeConvalidacion
{
    /**
     * @param  Collection<int, SolicitudConvalidacion>  $solicitudes
     * @return array{
     *     total: int,
     *     aprobadas: int,
     *     rechazadas: int,
     *     en_revision: int,
     *     enviadas: int,
     *     horas_aprobadas: int,
     *     horas_en_tramite: int,
     *     horas_rechazadas_no_otorgadas: int
     * }
     */
    public static function resumen(Collection $solicitudes): array
    {
        $aprobadas = $solicitudes->filter(fn (SolicitudConvalidacion $s) => $s->estado === EstadoSolicitudConvalidacion::Aprobada);
        $rechazadas = $solicitudes->filter(fn (SolicitudConvalidacion $s) => $s->estado === EstadoSolicitudConvalidacion::Rechazada);
        $enRevision = $solicitudes->filter(fn (SolicitudConvalidacion $s) => $s->estado === EstadoSolicitudConvalidacion::EnRevision);
        $enviadas = $solicitudes->filter(fn (SolicitudConvalidacion $s) => $s->estado === EstadoSolicitudConvalidacion::Enviada);

        $horasAprobadas = (int) $aprobadas->sum('horas_equivalentes');
        $horasEnTramite = (int) $enRevision->concat($enviadas)->sum('horas_equivalentes');
        $horasRechazadas = (int) $rechazadas->sum('horas_equivalentes');

        return [
            'total' => $solicitudes->count(),
            'aprobadas' => $aprobadas->count(),
            'rechazadas' => $rechazadas->count(),
            'en_revision' => $enRevision->count(),
            'enviadas' => $enviadas->count(),
            'horas_aprobadas' => $horasAprobadas,
            'horas_en_tramite' => $horasEnTramite,
            'horas_rechazadas_no_otorgadas' => $horasRechazadas,
        ];
    }

    /**
     * @param  resource  $salida
     * @param  Collection<int, SolicitudConvalidacion>  $solicitudes
     */
    public static function escribirInformeCompleto($salida, Semestre $semestre, Collection $solicitudes): void
    {
        $resumen = static::resumen($solicitudes);

        fputcsv($salida, ['INFORME DE CONVALIDACIONES']);
        fputcsv($salida, ['semestre', $semestre->nombre]);
        fputcsv($salida, ['periodo', $semestre->fecha_inicio->format('Y-m-d').' a '.$semestre->fecha_fin->format('Y-m-d')]);
        fputcsv($salida, ['generado_el', now()->format('Y-m-d H:i')]);
        fputcsv($salida, []);

        fputcsv($salida, ['RESUMEN']);
        fputcsv($salida, ['metrica', 'valor']);
        fputcsv($salida, ['total_solicitudes', $resumen['total']]);
        fputcsv($salida, ['solicitudes_aprobadas', $resumen['aprobadas']]);
        fputcsv($salida, ['solicitudes_rechazadas', $resumen['rechazadas']]);
        fputcsv($salida, ['solicitudes_en_revision', $resumen['en_revision']]);
        fputcsv($salida, ['solicitudes_enviadas', $resumen['enviadas']]);
        fputcsv($salida, ['horas_laborales_otorgadas_aprobadas', $resumen['horas_aprobadas']]);
        fputcsv($salida, ['horas_en_tramite_aprobadas_pendientes', $resumen['horas_en_tramite']]);
        fputcsv($salida, ['horas_equivalentes_rechazadas_no_otorgadas', $resumen['horas_rechazadas_no_otorgadas']]);
        fputcsv($salida, []);

        fputcsv($salida, ['DETALLE_TODAS_LAS_SOLICITUDES']);
        fputcsv($salida, [
            'cif',
            'semestre',
            'grupo',
            'porcentaje_asistencia',
            'sesiones_presentes',
            'sesiones_total',
            'horas_laborales',
            'estado',
            'estado_etiqueta',
            'fecha_envio',
            'fecha_revision',
        ]);

        foreach ($solicitudes->sortBy('id') as $solicitud) {
            fputcsv($salida, [
                $solicitud->miembro->cif,
                $semestre->nombre,
                $solicitud->miembro->juego?->nombre ?? '',
                $solicitud->porcentaje_asistencia,
                $solicitud->sesiones_presentes,
                $solicitud->sesiones_total,
                $solicitud->horas_equivalentes,
                $solicitud->estado->value,
                $solicitud->estado->etiqueta(),
                $solicitud->enviada_at->format('Y-m-d'),
                $solicitud->revisada_at?->format('Y-m-d') ?? '',
            ]);
        }
    }

    /**
     * @param  resource  $salida
     * @param  Collection<int, SolicitudConvalidacion>  $solicitudes  Solo aprobadas
     */
    public static function escribirRegistroAprobadas($salida, Semestre $semestre, Collection $solicitudes): void
    {
        fputcsv($salida, [
            'cif',
            'semestre',
            'grupo',
            'porcentaje_asistencia',
            'sesiones_presentes',
            'sesiones_total',
            'horas_laborales',
            'fecha_envio',
        ]);

        foreach ($solicitudes->sortBy('id') as $solicitud) {
            fputcsv($salida, [
                $solicitud->miembro->cif,
                $semestre->nombre,
                $solicitud->miembro->juego?->nombre ?? '',
                $solicitud->porcentaje_asistencia,
                $solicitud->sesiones_presentes,
                $solicitud->sesiones_total,
                $solicitud->horas_equivalentes,
                $solicitud->enviada_at->format('Y-m-d'),
            ]);
        }
    }
}
