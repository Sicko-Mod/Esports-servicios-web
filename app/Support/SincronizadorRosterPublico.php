<?php

namespace App\Support;

use App\Enums\Nivel;
use App\Models\Integrante;
use App\Models\Miembro;

/**
 * Mantiene el roster público (integrantes) alineado con perfiles del portal (miembros).
 */
final class SincronizadorRosterPublico
{
    public static function sincronizar(Miembro $miembro): void
    {
        $vinculados = Integrante::query()->where('miembro_id', $miembro->id)->get();

        if (! $miembro->perfilCompleto() || ! $miembro->juego_id || blank($miembro->gamer_tag)) {
            foreach ($vinculados as $integrante) {
                $integrante->delete();
            }

            return;
        }

        $existente = $vinculados->first();
        $nivel = $existente?->nivel ?? Nivel::Miembro;

        foreach ($vinculados->skip(1) as $duplicado) {
            $duplicado->delete();
        }

        $conflicto = Integrante::query()
            ->where('juego_id', $miembro->juego_id)
            ->where('gamer_tag', $miembro->gamer_tag)
            ->where(function ($q) use ($miembro) {
                $q->whereNull('miembro_id')
                    ->orWhere('miembro_id', '!=', $miembro->id);
            })
            ->first();

        if ($conflicto) {
            return;
        }

        Integrante::updateOrCreate(
            ['miembro_id' => $miembro->id],
            [
                'juego_id' => $miembro->juego_id,
                'gamer_tag' => $miembro->gamer_tag,
                'nivel' => $nivel,
            ]
        );

        Integrante::query()
            ->where('miembro_id', $miembro->id)
            ->where('juego_id', '!=', $miembro->juego_id)
            ->delete();
    }
}
