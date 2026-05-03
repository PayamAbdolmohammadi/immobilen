<?php

namespace App\Models;

use Database\Factories\ObjektFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Objekt extends Model
{
    /** @use HasFactory<ObjektFactory> */
    use HasFactory;

    protected $table = 'objekte';

    protected $fillable = ['mandant_id', 'name'];

    public function mandant(): BelongsTo
    {
        return $this->belongsTo(Mandant::class);
    }

    public function einheiten(): HasMany
    {
        return $this->hasMany(Einheit::class);
    }

    public function nkAbrechnungen(): HasMany
    {
        return $this->hasMany(NkAbrechnung::class);
    }
}
