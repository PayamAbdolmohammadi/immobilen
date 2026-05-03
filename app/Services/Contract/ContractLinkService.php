<?php

namespace App\Services\Contract;

use App\Models\Mietvertrag;

final class ContractLinkService
{
    public function createToken(Mietvertrag $vertrag): string
    {
        $plain = bin2hex(random_bytes(24));

        $updates = [
            'signature_token_hash' => hash('sha256', $plain),
            'token_expires_at' => now()->addDays(7),
        ];

        if ($vertrag->status === Mietvertrag::STATUS_DRAFT && $vertrag->sent_at === null) {
            $updates['sent_at'] = now();
            $updates['status'] = Mietvertrag::STATUS_SENT;
        }

        $vertrag->update($updates);

        return $plain;
    }

    public function validateToken(string $token): ?Mietvertrag
    {
        $token = trim($token);
        if ($token === '' || strlen($token) < 32) {
            return null;
        }

        $hash = hash('sha256', $token);

        /** @var Mietvertrag|null $lease */
        $lease = Mietvertrag::query()->where('signature_token_hash', $hash)->first();

        if ($lease === null) {
            return null;
        }

        if ($lease->token_expires_at !== null && $lease->token_expires_at->isPast()) {
            return null;
        }

        return $lease;
    }
}
