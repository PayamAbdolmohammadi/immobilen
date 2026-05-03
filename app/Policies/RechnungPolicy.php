<?php

namespace App\Policies;

use App\Models\Rechnung;
use App\Models\User;

class RechnungPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function view(User $user, Rechnung $rechnung): bool
    {
        return $user->mandant_id === $rechnung->mandant_id;
    }

    public function create(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function update(User $user, Rechnung $rechnung): bool
    {
        if ($user->mandant_id !== $rechnung->mandant_id) {
            return false;
        }

        // Rent invoices are immutable snapshots; corrections via Storno + new invoice later.
        if ($rechnung->typ === Rechnung::TYP_RENT) {
            return false;
        }

        return $rechnung->typ === Rechnung::TYP_MANUAL;
    }

    public function delete(User $user, Rechnung $rechnung): bool
    {
        return $user->mandant_id === $rechnung->mandant_id
            && $rechnung->typ === Rechnung::TYP_MANUAL
            && $rechnung->status === Rechnung::STATUS_OFFEN
            && $rechnung->allocatedCent() === 0;
    }

    public function sendMahnung(User $user, Rechnung $rechnung): bool
    {
        return $user->mandant_id === $rechnung->mandant_id
            && $rechnung->status === Rechnung::STATUS_OFFEN
            && $rechnung->isOverdue()
            && filled($rechnung->mieter?->email);
    }

    /**
     * Cancel an open rent invoice with no bank allocations (German bookkeeping Storno).
     */
    public function storno(User $user, Rechnung $rechnung): bool
    {
        if ($user->mandant_id !== $rechnung->mandant_id || ! $user->isOwner()) {
            return false;
        }

        return $rechnung->typ === Rechnung::TYP_RENT
            && $rechnung->status === Rechnung::STATUS_OFFEN
            && $rechnung->allocatedCent() === 0;
    }
}
