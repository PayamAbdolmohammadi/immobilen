<?php

namespace App\Policies;

use App\Models\NkAbrechnung;
use App\Models\User;

class NkAbrechnungPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function view(User $user, NkAbrechnung $nkAbrechnung): bool
    {
        return $user->mandant_id === $nkAbrechnung->mandant_id;
    }

    public function create(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function exportPdf(User $user, NkAbrechnung $nkAbrechnung): bool
    {
        return $this->view($user, $nkAbrechnung);
    }
}
