<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NkPosition extends Model
{
    protected $table = 'nk_positionen';

    protected $fillable = [
        'nk_abrechnung_id',
        'beschreibung',
        'betrag_cent',
    ];

    public function nkAbrechnung(): BelongsTo
    {
        return $this->belongsTo(NkAbrechnung::class, 'nk_abrechnung_id');
    }
}
