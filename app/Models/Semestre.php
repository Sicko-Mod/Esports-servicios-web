<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semestre extends Model
{
    protected $fillable = [
        'nombre', 'fecha_inicio', 'fecha_fin', 'minimo_sesiones', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'minimo_sesiones' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class);
    }

    public function scopeActivo(Builder $consulta): Builder
    {
        return $consulta->where('activo', true);
    }

    public static function actual(): ?self
    {
        return static::query()->activo()->orderByDesc('fecha_inicio')->first();
    }
}
