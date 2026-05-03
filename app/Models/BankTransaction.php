<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankTransaction extends Model
{
    public const STATUS_OFFEN = 'offen';

    public const STATUS_ZUGEORDNET = 'zugeordnet';

    protected $fillable = [
        'mandant_id',
        'bank_import_id',
        'buchungsdatum',
        'betrag_cent',
        'gegenpartei',
        'verwendungszweck',
        'raw_row',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'buchungsdatum' => 'date',
            'raw_row' => 'array',
        ];
    }

    public function mandant(): BelongsTo
    {
        return $this->belongsTo(Mandant::class);
    }

    public function bankImport(): BelongsTo
    {
        return $this->belongsTo(BankImport::class);
    }

    public function zahlungszuordnungen(): HasMany
    {
        return $this->hasMany(Zahlungszuordnung::class);
    }

    public function rechnungen(): BelongsToMany
    {
        return $this->belongsToMany(Rechnung::class, 'zahlungszuordnungen', 'bank_transaction_id', 'rechnung_id')
            ->withPivot(['betrag_cent', 'mandant_id'])
            ->withTimestamps();
    }

    public function allocatedCent(): int
    {
        return (int) $this->zahlungszuordnungen()->sum('betrag_cent');
    }

    public function remainingPayableCent(): int
    {
        if ($this->betrag_cent <= 0) {
            return 0;
        }

        return max(0, (int) $this->betrag_cent - $this->allocatedCent());
    }
}
