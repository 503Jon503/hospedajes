<?php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;

class ReservaPolicy
{
    public function view(User $user, Reserva $reserva): bool
    {
        return $user->esAdmin()
            || $user->id === $reserva->user_id
            || $user->id === $reserva->hospedaje->user_id;
    }

    public function update(User $user, Reserva $reserva): bool
    {
        return $user->esAdmin() || $user->id === $reserva->user_id;
    }
}