<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Auth\Access\HandlesAuthorization;
use Throwable;

class CitaPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($this->isRole($user, Roles::ADMIN)) {
            return true;
        }
    }

    public function view(User $user, Cita $cita)
    {
        if ($this->isRole($user, Roles::PROFESIONAL)) {
            return $user->id === $cita->medico_id;
        }
        return $user->id === $cita->paciente_id;
    }

    public function cancelar(User $user, Cita $cita)
    {
        if ($this->isRole($user, Roles::PROFESIONAL)) {
            return $user->id === $cita->medico_id;
        }
        return $user->id === $cita->paciente_id;
    }

    public function reprogramar(User $user, Cita $cita)
    {
        if ($this->isRole($user, Roles::PROFESIONAL)) {
            return $user->id === $cita->medico_id;
        }
        return $user->id === $cita->paciente_id;
    }

    public function completar(User $user, Cita $cita)
    {
        return $this->isRole($user, Roles::PROFESIONAL) && $user->id === $cita->medico_id;
    }

    public function agregarNotas(User $user, Cita $cita)
    {
        return $this->isRole($user, Roles::PROFESIONAL) && $user->id === $cita->medico_id;
    }

    private function isRole(User $user, string $role): bool
    {
        $legacy = strtolower((string) ($user->rol ?? ''));
        $aliases = match ($role) {
            Roles::PROFESIONAL => [Roles::PROFESIONAL, 'medico'],
            Roles::PACIENTE => [Roles::PACIENTE],
            Roles::ADMIN => [Roles::ADMIN],
            default => [$role],
        };

        if (in_array($legacy, $aliases, true)) {
            return true;
        }

        try {
            return $user->hasAnyRole($aliases);
        } catch (Throwable) {
            return false;
        }
    }
}
