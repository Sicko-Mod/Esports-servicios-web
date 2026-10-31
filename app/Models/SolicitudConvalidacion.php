<?php

namespace App\Models;

use App\Enums\EstadoSolicitudConvalidacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudConvalidacion extends Model
{
    protected $table = 'solicitudes_convalidacion';

    protected $fillable = [
        'miembro_id', 'semestre_id', 'estado',
        'porcentaje_asistencia', 'sesiones_presentes', 'sesiones_total', 'horas_equivalentes',
        'notas_miembro', 'notas_admin', 'revisado_por', 'enviada_at', 'revisada_at',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoSolicitudConvalidacion::class,
            'enviada_at' => 'datetime',
            'revisada_at' => 'datetime',
        ];
    }

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class);
    }

    public function revisadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function estaAbierta(): bool
    {
        return in_array($this->estado, [
            EstadoSolicitudConvalidacion::Enviada,
            EstadoSolicitudConvalidacion::EnRevision,
        ], true);
    }
}
