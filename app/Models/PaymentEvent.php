<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentEvent extends Model
{
    protected $fillable = [
        'event_id',
        'order_public_id',
        'order_id',
        'status',
        'amount',
        'currency',
        'event_created_at',
        'processed',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'event_created_at' => 'datetime',
            'processed' => 'boolean',
            'payload' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
