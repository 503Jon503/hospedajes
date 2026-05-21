<?php

namespace App\Policies;

use App\Models\Hospedaje;
use App\Models\User;

class HospedajePolicy
{
    public function update(User $user, Hospedaje $hospedaje): bool
    {
        return $user->esAdmin() || $user->id === $hospedaje->user_id;
    }

    public function delete(User $user, Hospedaje $hospedaje): bool
    {
        return $user->esAdmin() || $user->id === $hospedaje->user_id;
    }
}