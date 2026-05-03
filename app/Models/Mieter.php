<?php

namespace App\Models;

use Database\Factories\MieterFactory;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mieter extends Model implements AuthenticatableContract
{
    /** @use HasFactory<MieterFactory> */
    use Authenticatable;
    use HasFactory;

    protected $table = 'mieter';

    protected $fillable = ['mandant_id', 'name', 'email', 'password'];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function mandant(): BelongsTo
    {
        return $this->belongsTo(Mandant::class);
    }

    public function rechnungen(): HasMany
    {
        return $this->hasMany(Rechnung::class);
    }
}
