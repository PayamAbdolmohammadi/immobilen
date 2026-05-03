<?php

namespace App\Policies;

use App\Models\Einheit;
use App\Models\User;

class EinheitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function view(User $user, Einheit $einheit): bool
    {
        return $user->mandant_id === $einheit->mandant_id;
    }

    public function create(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function update(User $user, Einheit $einheit): bool
    {
        return $user->mandant_id === $einheit->mandant_id;
    }

    public function delete(User $user, Einheit $einheit): bool
    {
        return $user->isOwner() && $user->mandant_id === $einheit->mandant_id;
    }
}
