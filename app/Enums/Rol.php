<?php

namespace App\Enums;

enum Rol: string
{
    case Miembro = 'miembro';
    case Admin = 'admin';
    case Superadmin = 'superadmin';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Miembro => 'Miembro',
            self::Admin => 'Administrador',
            self::Superadmin => 'Superadministrador',
        };
    }

    public function puedeAdministrarPanel(): bool
    {
        return match ($this) {
            self::Admin, self::Superadmin => true,
            self::Miembro => false,
        };
    }

    public function puedeGestionarRoles(): bool
    {
        return $this === self::Superadmin;
    }
}
