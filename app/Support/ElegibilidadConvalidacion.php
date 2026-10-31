<?php

namespace App\Support;

use App\Models\Miembro;
use App\Models\Semestre;
use App\Models\SolicitudConvalidacion;

final class ElegibilidadConvalidacion
{
    /**
     * @return array{elegible: bool, motivos: list<string>, resumen: array|null}
     */
    public static function evaluar(Miembro $miembro, ?Semestre $semestre): array
    {
        $motivos = [];

        if (! $semestre) {
            return ['elegible' => false, 'motivos' => ['No hay un semestre activo.'], 'resumen' => null];
        }

        if (! $miembro->perfilCompleto() || ! $miembro->juego_id) {
            $motivos[] = 'Completá tu perfil y elegí un grupo.';
        }

        $resumen = CalculadoraAsistencia::paraMiembro($miembro, $semestre);

        if ($resumen['total_sesiones'] === 0) {
            $motivos[] = 'Tu grupo aún no tiene sesiones registradas este semestre.';
        }

        if ($semestre->minimo_sesiones && $resumen['total_sesiones'] < $semestre->minimo_sesiones) {
            $motivos[] = 'El semestre aún no alcanza el mínimo de sesiones institucional ('.$semestre->minimo_sesiones.').';
        }

        if (! $resumen['cumple_umbral']) {
            $motivos[] = 'Necesitás al menos '.config('club.umbral_asistencia').'% de asistencia (vas en '.$resumen['porcentaje'].').';
        }

        $solicitudExistente = SolicitudConvalidacion::query()
            ->where('miembro_id', $miembro->id)
            ->where('semestre_id', $semestre->id)
            ->first();

        if ($solicitudExistente) {
            $motivos[] = 'Ya enviaste una solicitud para este semestre ('.$solicitudExistente->estado->etiqueta().').';
        }

        return [
            'elegible' => $motivos === [],
            'motivos' => $motivos,
            'resumen' => $resumen,
            'solicitud' => $solicitudExistente,
        ];
    }
}
