<?php

namespace App\Models;

use App\Enums\EstadoAsistencia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    protected $fillable = [
        'sesion_id', 'miembro_id', 'estado', 'marcado_por',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoAsistencia::class,
        ];
    }

    public function sesion(): BelongsTo
    {
        return $this->belongsTo(Sesion::class);
    }

    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class);
    }

    public function marcadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marcado_por');
    }
}
