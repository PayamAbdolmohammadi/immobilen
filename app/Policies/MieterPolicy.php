<?php

namespace App\Policies;

use App\Models\Mieter;
use App\Models\User;

class MieterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function view(User $user, Mieter $mieter): bool
    {
        return $user->mandant_id === $mieter->mandant_id;
    }

    public function create(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function update(User $user, Mieter $mieter): bool
    {
        return $user->mandant_id === $mieter->mandant_id;
    }

    public function delete(User $user, Mieter $mieter): bool
    {
        return $user->isOwner() && $user->mandant_id === $mieter->mandant_id;
    }
}
