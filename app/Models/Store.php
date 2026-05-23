<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'slug', 'description',
        'logo_path', 'banner_path', 'has_fixed_location',
        'latitude', 'longitude', 'location_label', 'status',
    ];

    protected function casts(): array
    {
        return [
            'has_fixed_location' => 'boolean',
            'latitude'           => 'float',
            'longitude'          => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo_path
            ? asset('storage/' . $this->logo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=16a34a&color=fff&size=128';
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner_path ? asset('storage/' . $this->banner_path) : null;
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function getAverageRatingAttribute(): float
    {
        // Average across all seller reviews
        return round(
            Review::whereHas('order', fn ($q) => $q->where('seller_id', $this->user_id))
                ->where('reviewee_id', $this->user_id)
                ->avg('rating') ?? 0,
            1
        );
    }
}
