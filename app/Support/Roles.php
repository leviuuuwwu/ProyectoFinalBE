<?php

namespace App\Support;

final class Roles
{
    public const ADMIN = 'admin';

    public const PROFESIONAL = 'profesional';

    public const PACIENTE = 'paciente';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [self::ADMIN, self::PROFESIONAL, self::PACIENTE];
    }

    /**
     * Roles que solo un administrador puede asignar al crear usuarios desde el panel.
     *
     * @return list<string>
     */
    public static function creablesPorAdmin(): array
    {
        return [self::ADMIN, self::PROFESIONAL];
    }
}
