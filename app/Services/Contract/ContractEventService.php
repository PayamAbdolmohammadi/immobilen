<?php

namespace App\Services\Contract;

use App\Models\ContractEvent;
use App\Models\Mietvertrag;
use Illuminate\Http\Request;

final class ContractEventService
{
    /**
     * @param  array<string, mixed>|null  $payload
     */
    public function log(Mietvertrag $vertrag, string $eventType, string $actorType, ?int $actorId = null, ?array $payload = null, ?Request $request = null): ContractEvent
    {
        $req = $request ?? request();

        return ContractEvent::query()->create([
            'mandant_id' => $vertrag->mandant_id,
            'mietvertrag_id' => $vertrag->id,
            'event_type' => $eventType,
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'ip_address' => $req?->ip(),
            'user_agent' => $req ? substr((string) $req->userAgent(), 0, 2000) : null,
            'payload' => $payload,
        ]);
    }
}
