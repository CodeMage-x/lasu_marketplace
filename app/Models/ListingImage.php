<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingImage extends Model
{
    protected $fillable = ['listing_id', 'image_path', 'is_primary', 'sort_order'];

    protected function casts(): array { return ['is_primary' => 'boolean']; }

    public function listing(): BelongsTo { return $this->belongsTo(Listing::class); }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}
