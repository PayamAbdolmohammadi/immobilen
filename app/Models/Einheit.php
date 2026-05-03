<?php

namespace App\Models;

use Database\Factories\EinheitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Einheit extends Model
{
    /** @use HasFactory<EinheitFactory> */
    use HasFactory;

    protected $table = 'einheiten';

    protected $fillable = ['mandant_id', 'objekt_id', 'name'];

    public function mandant(): BelongsTo
    {
        return $this->belongsTo(Mandant::class);
    }

    public function objekt(): BelongsTo
    {
        return $this->belongsTo(Objekt::class);
    }

    public function mietvertraege(): HasMany
    {
        return $this->hasMany(Mietvertrag::class);
    }

    public function rechnungen(): HasMany
    {
        return $this->hasMany(Rechnung::class);
    }

    public function activeMietvertrag(): ?Mietvertrag
    {
        return $this->mietvertraege()->where('status', Mietvertrag::STATUS_ACTIVE)->first();
    }
}
