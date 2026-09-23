<?php

namespace App\Models;

use App\Enums\Nivel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Integrante del roster público. Antes: post type «jg_miembro».
 *
 * Solo datos públicos: gamer tag y, únicamente con permiso expreso y si es
 * mayor de edad, el nombre real. Nunca CIF, teléfono ni fecha de nacimiento.
 */
class Integrante extends Model
{
    protected $table = 'integrantes';

    protected $fillable = ['juego_id', 'gamer_tag', 'nivel', 'nombre_publico'];

    protected function casts(): array
    {
        return [
            'nivel' => Nivel::class,
        ];
    }

    public function juego(): BelongsTo
    {
        return $this->belongsTo(Juego::class);
    }
}
