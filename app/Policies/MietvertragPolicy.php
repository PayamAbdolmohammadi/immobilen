<?php

namespace App\Policies;

use App\Models\Mietvertrag;
use App\Models\User;

class MietvertragPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function view(User $user, Mietvertrag $mietvertrag): bool
    {
        return $user->mandant_id === $mietvertrag->mandant_id;
    }

    public function create(User $user): bool
    {
        return $user->mandant_id !== null;
    }

    public function update(User $user, Mietvertrag $mietvertrag): bool
    {
        return $user->mandant_id === $mietvertrag->mandant_id
            && in_array($mietvertrag->status, [
                Mietvertrag::STATUS_ACTIVE,
                Mietvertrag::STATUS_DRAFT,
                Mietvertrag::STATUS_SENT,
            ], true);
    }

    public function end(User $user, Mietvertrag $mietvertrag): bool
    {
        return $user->mandant_id === $mietvertrag->mandant_id
            && $mietvertrag->status === Mietvertrag::STATUS_ACTIVE;
    }
}
