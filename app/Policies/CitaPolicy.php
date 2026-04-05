<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CitaPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->rol === 'admin') {
            return true;
        }
    }

    public function view(User $user, Cita $cita)
    {
        if ($user->rol === 'medico') {
            return $user->id === $cita->medico_id;
        }
        return $user->id === $cita->paciente_id;
    }

    public function cancelar(User $user, Cita $cita)
    {
        return $user->id === $cita->paciente_id || $user->id === $cita->medico_id;
    }

    public function reprogramar(User $user, Cita $cita)
    {
        return $user->id === $cita->paciente_id || $user->id === $cita->medico_id;
    }

    public function completar(User $user, Cita $cita)
    {
        return $user->rol === 'medico' && $user->id === $cita->medico_id;
    }
}