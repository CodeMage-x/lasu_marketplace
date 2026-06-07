<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    // role and status are intentionally excluded — use forceFill() or direct
    // property assignment in the few places that legitimately set them (VULN-16)
    protected $fillable = [
        'name', 'email', 'edu_email', 'password',
        'faculty', 'department', 'phone', 'avatar', 'is_external_login', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'last_login_at'      => 'datetime',
            'is_external_login'  => 'boolean',
            'password'           => 'hashed',
        ];
    }

    // ── Role helpers ───────────────────────────────────────────────────────────
    public function isAdmin(): bool   { return $this->role === 'admin';  }
    public function isSeller(): bool  { return $this->role === 'seller'; }
    public function isBuyer(): bool   { return $this->role === 'buyer';  }
    public function isActive(): bool  { return $this->status === 'active'; }

    // ── Relationships ──────────────────────────────────────────────────────────
    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function ordersAsBuyer(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function ordersAsSeller(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function conversationsAsBuyer(): HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    public function conversationsAsSeller(): HasMany
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    /**
     * Return avatar URL — generate a local SVG data-URI for users without a custom avatar
     * instead of leaking the user's name to an external service (VULN-12).
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Derive two initials from the name
        $words    = preg_split('/\s+/', trim($this->name));
        $initials = strtoupper(
            count($words) >= 2
                ? substr($words[0], 0, 1) . substr($words[1], 0, 1)
                : substr($words[0], 0, 2)
        );

        // Deterministic background colour based on name (no external request)
        $colours  = ['0a6640', '1d4ed8', '7c3aed', 'b45309', 'be185d', '0e7490', '166534'];
        $colour   = $colours[abs(crc32($this->name)) % count($colours)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">'
             . '<rect width="64" height="64" fill="#' . $colour . '"/>'
             . '<text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" '
             . 'fill="#fff" font-size="26" font-family="sans-serif" font-weight="bold">'
             . htmlspecialchars($initials, ENT_XML1)
             . '</text></svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviewsReceived()->avg('rating') ?? 0, 1);
    }
}
