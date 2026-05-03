<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractEvent extends Model
{
    public const ACTOR_OWNER = 'owner';

    public const ACTOR_TENANT = 'tenant';

    public const ACTOR_SYSTEM = 'system';

    protected $fillable = [
        'mandant_id',
        'mietvertrag_id',
        'event_type',
        'actor_type',
        'actor_id',
        'ip_address',
        'user_agent',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function mandant(): BelongsTo
    {
        return $this->belongsTo(Mandant::class);
    }

    public function mietvertrag(): BelongsTo
    {
        return $this->belongsTo(Mietvertrag::class);
    }
}
