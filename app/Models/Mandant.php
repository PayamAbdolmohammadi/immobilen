<?php

namespace App\Models;

use Database\Factories\MandantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mandant extends Model
{
    /** @use HasFactory<MandantFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    public function mieter(): HasMany
    {
        return $this->hasMany(Mieter::class);
    }

    public function einheiten(): HasMany
    {
        return $this->hasMany(Einheit::class);
    }

    public function mietvertraege(): HasMany
    {
        return $this->hasMany(Mietvertrag::class);
    }

    public function objekte(): HasMany
    {
        return $this->hasMany(Objekt::class);
    }

    public function rechnungen(): HasMany
    {
        return $this->hasMany(Rechnung::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function bankImports(): HasMany
    {
        return $this->hasMany(BankImport::class);
    }

    public function bankTransactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class);
    }

    public function zahlungszuordnungen(): HasMany
    {
        return $this->hasMany(Zahlungszuordnung::class);
    }

    public function nkAbrechnungen(): HasMany
    {
        return $this->hasMany(NkAbrechnung::class);
    }
}
