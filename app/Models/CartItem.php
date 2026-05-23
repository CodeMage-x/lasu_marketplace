<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'listing_id', 'quantity'];

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function listing(): BelongsTo { return $this->belongsTo(Listing::class); }

    public function getSubtotalAttribute(): float
    {
        return $this->quantity * ($this->listing->price ?? 0);
    }
}
