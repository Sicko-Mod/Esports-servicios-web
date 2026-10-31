<?php

namespace App\Support;

use App\Enums\EstadoAsistencia;
use App\Models\Miembro;
use App\Models\Semestre;

final class CalculadoraAsistencia
{
    /**
     * @return array{presentes: int, justificados: int, total_sesiones: int, porcentaje: float, cumple_umbral: bool}
     */
    public static function paraMiembro(Miembro $miembro, Semestre $semestre): array
    {
        $vacio = [
            'presentes' => 0,
            'justificados' => 0,
            'total_sesiones' => 0,
            'porcentaje' => 0.0,
            'cumple_umbral' => false,
        ];

        $miembro->loadMissing('juego');

        if (! $miembro->juego_id) {
            return $vacio;
        }

        $sesionIds = $miembro->juego
            ->sesiones()
            ->where('semestre_id', $semestre->id)
            ->pluck('id');

        $total = $sesionIds->count();

        if ($total === 0) {
            return $vacio;
        }

        $conteo = $miembro->asistencias()
            ->whereIn('sesion_id', $sesionIds)
            ->selectRaw('estado, count(*) as cantidad')
            ->groupBy('estado')
            ->pluck('cantidad', 'estado');

        $presentes = (int) ($conteo[EstadoAsistencia::Presente->value] ?? 0);
        $justificados = (int) ($conteo[EstadoAsistencia::Justificado->value] ?? 0);
        $porcentaje = self::porcentaje($presentes, $justificados, $total);

        return [
            'presentes' => $presentes,
            'justificados' => $justificados,
            'total_sesiones' => $total,
            'porcentaje' => $porcentaje,
            'cumple_umbral' => self::cumpleUmbral($porcentaje),
        ];
    }

    /**
     * By default a justified absence still counts as an absence. With
     * club.justificados_descuentan_del_total it is removed from the base instead.
     */
    public static function porcentaje(int $presentes, int $justificados, int $totalSesiones): float
    {
        $base = config('club.justificados_descuentan_del_total')
            ? $totalSesiones - $justificados
            : $totalSesiones;

        if ($base <= 0) {
            return 0.0;
        }

        return round(($presentes / $base) * 100, 1);
    }

    public static function cumpleUmbral(float $porcentaje): bool
    {
        return $porcentaje >= (int) config('club.umbral_asistencia');
    }
}
