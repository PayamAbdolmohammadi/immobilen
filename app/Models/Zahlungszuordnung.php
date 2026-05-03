<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Zahlungszuordnung extends Model
{
    protected $table = 'zahlungszuordnungen';

    protected $fillable = [
        'mandant_id',
        'bank_transaction_id',
        'rechnung_id',
        'betrag_cent',
    ];

    public function mandant(): BelongsTo
    {
        return $this->belongsTo(Mandant::class);
    }

    public function bankTransaction(): BelongsTo
    {
        return $this->belongsTo(BankTransaction::class);
    }

    public function rechnung(): BelongsTo
    {
        return $this->belongsTo(Rechnung::class);
    }
}
