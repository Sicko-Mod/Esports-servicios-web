<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Miembro extends Model
{
    protected $fillable = [
        'user_id', 'juego_id', 'gamer_tag', 'cif', 'carrera', 'telefono',
        'fecha_nacimiento', 'tutor_nombre', 'tutor_telefono', 'tutor_consentimiento_at',
        'perfil_completado_at',
    ];

    protected function casts(): array
    {
        return [
            'cif' => 'encrypted',
            'fecha_nacimiento' => 'date',
            'tutor_consentimiento_at' => 'datetime',
            'perfil_completado_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function juego(): BelongsTo
    {
        return $this->belongsTo(Juego::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    public function integrantePublico(): HasOne
    {
        return $this->hasOne(Integrante::class);
    }

    public function solicitudesConvalidacion(): HasMany
    {
        return $this->hasMany(SolicitudConvalidacion::class);
    }

    public function esMenor(): bool
    {
        if (! $this->fecha_nacimiento) {
            return false;
        }

        return $this->fecha_nacimiento->age < 18;
    }

    public function perfilCompleto(): bool
    {
        return $this->perfil_completado_at !== null;
    }

    public function marcarPerfilCompleto(): void
    {
        $this->forceFill(['perfil_completado_at' => now()])->save();
    }
}
