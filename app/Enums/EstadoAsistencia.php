<?php

namespace App\Enums;

enum EstadoAsistencia: string
{
    case Presente = 'presente';
    case Ausente = 'ausente';
    case Justificado = 'justificado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Presente => 'Presente',
            self::Ausente => 'Ausente',
            self::Justificado => 'Justificado',
        };
    }
}
