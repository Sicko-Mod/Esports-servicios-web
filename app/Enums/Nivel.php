<?php

namespace App\Enums;

/**
 * Niveles del roster. Equivale a la taxonomía «jg_tier» del plugin de WordPress.
 */
enum Nivel: string
{
    case Tier1 = 'tier-1';
    case Tier2 = 'tier-2';
    case Miembro = 'miembro';

    public function titulo(): string
    {
        return match ($this) {
            self::Tier1 => 'Tier 1',
            self::Tier2 => 'Tier 2',
            self::Miembro => 'Miembros',
        };
    }
    public function nota(): string
    {
        return match ($this) {
            self::Tier1 => 'Representa a la universidad',
            self::Tier2 => 'Segundo equipo',
            self::Miembro => '',
        };
    }
}
