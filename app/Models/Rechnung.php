<?php

namespace App\Models;

use Database\Factories\RechnungFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rechnung extends Model
{
    /** @use HasFactory<RechnungFactory> */
    use HasFactory;

    protected $table = 'rechnungen';

    public const TYP_RENT = 'rent';

    public const TYP_MANUAL = 'manual';

    public const STATUS_OFFEN = 'offen';

    public const STATUS_BEZAHLT = 'bezahlt';

    public const STATUS_STORNIERT = 'storniert';

    protected $fillable = [
        'mandant_id',
        'mieter_id',
        'einheit_id',
        'mietvertrag_id',
        'typ',
        'billing_period',
        'betrag_cent',
        'source_data',
        'status',
        'faellig_am',
        'bezahlt_am',
        'storniert_am',
    ];

    protected function casts(): array
    {
        return [
            'source_data' => 'array',
            'faellig_am' => 'date',
            'bezahlt_am' => 'datetime',
            'storniert_am' => 'datetime',
        ];
    }

    public function mandant(): BelongsTo
    {
        return $this->belongsTo(Mandant::class);
    }

    public function mieter(): BelongsTo
    {
        return $this->belongsTo(Mieter::class);
    }

    public function einheit(): BelongsTo
    {
        return $this->belongsTo(Einheit::class);
    }

    public function mietvertrag(): BelongsTo
    {
        return $this->belongsTo(Mietvertrag::class, 'mietvertrag_id');
    }

    public function zahlungszuordnungen(): HasMany
    {
        return $this->hasMany(Zahlungszuordnung::class);
    }

    public function bankTransactions(): BelongsToMany
    {
        return $this->belongsToMany(BankTransaction::class, 'zahlungszuordnungen', 'rechnung_id', 'bank_transaction_id')
            ->withPivot(['betrag_cent', 'mandant_id'])
            ->withTimestamps();
    }

    public function allocatedCent(): int
    {
        return (int) $this->zahlungszuordnungen()->sum('betrag_cent');
    }

    public function openAmountCent(): int
    {
        if ($this->status === self::STATUS_STORNIERT) {
            return 0;
        }

        return max(0, (int) $this->betrag_cent - $this->allocatedCent());
    }

    public function isOverdue(): bool
    {
        if ($this->status !== self::STATUS_OFFEN || $this->faellig_am === null) {
            return false;
        }

        return $this->faellig_am->lt(now()->startOfDay());
    }

    public function scopeRent(Builder $query): Builder
    {
        return $query->where('typ', self::TYP_RENT);
    }

    public function scopeManual(Builder $query): Builder
    {
        return $query->where('typ', self::TYP_MANUAL);
    }

    public function scopeForPeriod(Builder $query, string $billingPeriod): Builder
    {
        return $query->where('billing_period', $billingPeriod);
    }
}
