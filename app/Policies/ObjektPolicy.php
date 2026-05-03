<?php

namespace App\Policies;

use App\Models\Objekt;
use App\Models\User;

class ObjektPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function view(User $user, Objekt $objekt): bool
    {
        return $user->mandant_id === $objekt->mandant_id;
    }

    public function create(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function update(User $user, Objekt $objekt): bool
    {
        return $user->mandant_id === $objekt->mandant_id;
    }

    public function delete(User $user, Objekt $objekt): bool
    {
        return $user->isOwner() && $user->mandant_id === $objekt->mandant_id;
    }
}
