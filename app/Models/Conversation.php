<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'listing_id', 'buyer_id', 'seller_id',
        'last_message_at', 'archived_by_buyer_at', 'archived_by_seller_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at'         => 'datetime',
            'archived_by_buyer_at'    => 'datetime',
            'archived_by_seller_at'   => 'datetime',
        ];
    }

    public function listing(): BelongsTo  { return $this->belongsTo(Listing::class); }
    public function buyer(): BelongsTo    { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller(): BelongsTo   { return $this->belongsTo(User::class, 'seller_id'); }
    public function messages(): HasMany   { return $this->hasMany(Message::class)->orderBy('created_at'); }
    public function meetupProposals(): HasMany { return $this->hasMany(MeetupProposal::class); }
    public function orders(): HasMany     { return $this->hasMany(Order::class); }

    public function getOtherParticipant(User $user): User
    {
        return $user->id === $this->buyer_id ? $this->seller : $this->buyer;
    }

    public function unreadCountFor(User $user): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}
