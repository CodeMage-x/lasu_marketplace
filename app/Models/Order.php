<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'buyer_id', 'seller_id',
        'conversation_id', 'meetup_proposal_id',
        'payment_method', 'order_status', 'payment_status',
        'subtotal', 'total_amount', 'seller_name_snapshot',
        'confirmed_at', 'handed_over_at', 'completed_at', 'cancelled_at', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'      => 'decimal:2',
            'total_amount'  => 'decimal:2',
            'confirmed_at'  => 'datetime',
            'handed_over_at'=> 'datetime',
            'completed_at'  => 'datetime',
            'cancelled_at'  => 'datetime',
            'paid_at'       => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────
    public function buyer(): BelongsTo           { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller(): BelongsTo          { return $this->belongsTo(User::class, 'seller_id'); }
    public function conversation(): BelongsTo    { return $this->belongsTo(Conversation::class); }
    public function meetupProposal(): BelongsTo  { return $this->belongsTo(MeetupProposal::class); }
    public function items(): HasMany             { return $this->hasMany(OrderItem::class); }
    public function payment(): HasOne            { return $this->hasOne(Payment::class); }
    public function review(): HasOne             { return $this->hasOne(Review::class); }

    // ── Status helpers ─────────────────────────────────────────────────────────
    public function isPending(): bool    { return $this->order_status === 'pending'; }
    public function isConfirmed(): bool  { return $this->order_status === 'confirmed'; }
    public function isCompleted(): bool  { return $this->order_status === 'completed'; }
    public function isCancelled(): bool  { return $this->order_status === 'cancelled'; }
    public function isPaid(): bool       { return $this->payment_status === 'paid'; }

    // ── Accessors ──────────────────────────────────────────────────────────────
    public function getFormattedTotalAttribute(): string
    {
        return '₦' . number_format($this->total_amount, 2);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->order_status) {
            'pending'     => 'badge-warning',
            'confirmed'   => 'badge-info',
            'handed_over' => 'badge-primary',
            'completed'   => 'badge-success',
            'cancelled'   => 'badge-danger',
            'disputed'    => 'badge-dark',
            default       => 'badge-secondary',
        };
    }

    // ── Static helpers ─────────────────────────────────────────────────────────
    // Use a UUID so order numbers are not enumerable (VULN-13)
    public static function generateOrderNumber(): string
    {
        return 'LASU-' . strtoupper(str_replace('-', '', Str::uuid()));
    }
}
