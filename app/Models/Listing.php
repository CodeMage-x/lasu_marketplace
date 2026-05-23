<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'store_id', 'category_id', 'campus_zone_id',
        'title', 'description', 'price', 'is_negotiable', 'is_preorder',
        'item_condition', 'stock_quantity', 'availability',
        'view_count', 'published_at', 'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'price'         => 'decimal:2',
            'is_negotiable' => 'boolean',
            'is_preorder'   => 'boolean',
            'published_at'  => 'datetime',
            'sold_at'       => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────
    public function user(): BelongsTo      { return $this->belongsTo(User::class); }
    public function store(): BelongsTo     { return $this->belongsTo(Store::class); }
    public function category(): BelongsTo  { return $this->belongsTo(Category::class); }
    public function campusZone(): BelongsTo{ return $this->belongsTo(CampusZone::class); }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ListingImage::class)->where('is_primary', true);
    }

    public function cartItems(): HasMany   { return $this->hasMany(CartItem::class); }
    public function orderItems(): HasMany  { return $this->hasMany(OrderItem::class); }
    public function conversations(): HasMany { return $this->hasMany(Conversation::class); }

    // ── Accessors ──────────────────────────────────────────────────────────────
    public function getPrimaryImageUrlAttribute(): string
    {
        $img = $this->images->firstWhere('is_primary', true) ?? $this->images->first();
        return $img ? asset('storage/' . $img->image_path) : asset('images/placeholder.png');
    }

    public function getFormattedPriceAttribute(): string
    {
        return '₦' . number_format($this->price, 2);
    }

    public function isAvailable(): bool
    {
        return $this->availability === 'available' && $this->stock_quantity > 0;
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('availability', '!=', 'sold');
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability', 'available')->where('stock_quantity', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
