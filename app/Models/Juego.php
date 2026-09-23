<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Un grupo del club (Valorant, Overwatch, etc.). Antes: post type «jg_juego».
 */
class Juego extends Model
{
    protected $table = 'juegos';

    public const DIAS = [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sabado' => 'Sábado',
        'domingo' => 'Domingo',
    ];

    protected $fillable = [
        'nombre', 'slug', 'descripcion', 'dia', 'hora', 'lugar', 'capitan', 'activo', 'orden',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'orden' => 'integer',
        ];
    }

    public function integrantes(): HasMany
    {
        return $this->hasMany(Integrante::class)->orderBy('gamer_tag');
    }

    public function torneos(): HasMany
    {
        return $this->hasMany(Torneo::class);
    }

    public function scopeActivos(Builder $consulta): Builder
    {
        return $consulta->where('activo', true);
    }

    public function scopeOrdenados(Builder $consulta): Builder
    {
        return $consulta->orderBy('orden')->orderBy('nombre');
    }

    public function getDiaLegibleAttribute(): string
    {
        return self::DIAS[$this->dia] ?? '';
    }

    public function getHoraLegibleAttribute(): string
    {
        if (! $this->hora) {
            return '';
        }

        return Carbon::parse($this->hora)->format('g:i a');
    }

    /**
     * Roster agrupado por nivel: ['tier-1' => [...], 'tier-2' => [...], 'miembro' => [...]].
     */
    public function rosterPorNivel(): Collection
    {
        return $this->integrantes->groupBy(fn (Integrante $integrante) => $integrante->nivel->value);
    }
}
