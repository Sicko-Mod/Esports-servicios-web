<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Torneo del club. Antes: post type «jg_torneo».
 */
class Torneo extends Model
{
    protected $table = 'torneos';

    protected $fillable = [
        'juego_id', 'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin',
        'modalidad', 'abierto_externos', 'enlace_inscripcion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'abierto_externos' => 'boolean',
        ];
    }

    public function juego(): BelongsTo
    {
        return $this->belongsTo(Juego::class);
    }

    public function scopeProximos(Builder $consulta): Builder
    {
        return $consulta->whereDate('fecha_inicio', '>=', now()->toDateString())
            ->orderBy('fecha_inicio');
    }

    public function scopePasados(Builder $consulta): Builder
    {
        return $consulta->whereDate('fecha_inicio', '<', now()->toDateString())
            ->orderByDesc('fecha_inicio');
    }
}
