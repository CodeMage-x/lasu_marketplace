<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'provider', 'provider_reference',
        'amount', 'currency', 'status', 'gateway_payload', 'paid_at', 'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'          => 'decimal:2',
            'gateway_payload' => 'array',
            'paid_at'         => 'datetime',
            'failed_at'       => 'datetime',
        ];
    }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function user(): BelongsTo  { return $this->belongsTo(User::class); }

    public function isSuccessful(): bool { return $this->status === 'success'; }
}
