<?php

namespace App\Enums;

enum EstadoSolicitudConvalidacion: string
{
    case Enviada = 'enviada';
    case EnRevision = 'en_revision';
    case Aprobada = 'aprobada';
    case Rechazada = 'rechazada';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Enviada => 'Enviada',
            self::EnRevision => 'En revisión',
            self::Aprobada => 'Aprobada',
            self::Rechazada => 'Rechazada',
        };
    }
}
