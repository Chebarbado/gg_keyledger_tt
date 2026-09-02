<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'currency',
        'max_uses',
        'used_count',
    ];

    public function uses(): HasMany
    {
        return $this->hasMany(PromoCodeUse::class);
    }
}
