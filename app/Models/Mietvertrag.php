<?php

namespace App\Models;

use Database\Factories\MietvertragFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mietvertrag extends Model
{
    /** @use HasFactory<MietvertragFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SENT = 'sent';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_ENDED = 'ended';

    protected $table = 'mietvertraege';

    protected $fillable = [
        'mandant_id',
        'einheit_id',
        'mieter_id',
        'starts_on',
        'ends_on',
        'status',
        'kaltmiete_cent',
        'nebenkosten_vorauszahlung_cent',
        'zahlungsintervall',
        'faelligkeit_tag',
        'next_billing_period',
        'last_billed_at',
        'sent_at',
        'accepted_at',
        'activated_at',
        'contract_pdf_path',
        'signature_token_hash',
        'token_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'last_billed_at' => 'datetime',
            'sent_at' => 'datetime',
            'accepted_at' => 'datetime',
            'activated_at' => 'datetime',
            'token_expires_at' => 'datetime',
        ];
    }

    public function mandant(): BelongsTo
    {
        return $this->belongsTo(Mandant::class);
    }

    public function einheit(): BelongsTo
    {
        return $this->belongsTo(Einheit::class);
    }

    public function mieter(): BelongsTo
    {
        return $this->belongsTo(Mieter::class);
    }

    public function rechnungen(): HasMany
    {
        return $this->hasMany(Rechnung::class, 'mietvertrag_id');
    }

    public function contractEvents(): HasMany
    {
        return $this->hasMany(ContractEvent::class)->orderByDesc('created_at');
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Monthly rent invoices may be generated only for leases that passed Phase 8 workflow or grandfathered legacy rows.
     */
    public function canGenerateInvoices(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->activated_at !== null || $this->accepted_at !== null) {
            return true;
        }

        return $this->sent_at === null && $this->signature_token_hash === null;
    }
}
