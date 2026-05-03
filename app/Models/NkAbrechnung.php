<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NkAbrechnung extends Model
{
    public const VERTEILUNG_GLEICH_PRO_EINHEIT = 'gleich_pro_einheit';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_FINAL = 'final';

    protected $table = 'nk_abrechnungen';

    protected $fillable = [
        'mandant_id',
        'objekt_id',
        'jahr',
        'verteilungs_art',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'jahr' => 'integer',
        ];
    }

    public function mandant(): BelongsTo
    {
        return $this->belongsTo(Mandant::class);
    }

    public function objekt(): BelongsTo
    {
        return $this->belongsTo(Objekt::class);
    }

    public function positionen(): HasMany
    {
        return $this->hasMany(NkPosition::class, 'nk_abrechnung_id');
    }

    public function totalCent(): int
    {
        return (int) $this->positionen()->sum('betrag_cent');
    }

    public function einheitenCount(): int
    {
        return max(1, $this->objekt->einheiten()->count());
    }

    /**
     * Equal split across units on this property (MVP).
     */
    public function anteilProEinheitCent(): int
    {
        return intdiv($this->totalCent(), $this->einheitenCount());
    }
}
