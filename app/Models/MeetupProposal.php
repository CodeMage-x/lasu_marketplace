<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetupProposal extends Model
{
    protected $fillable = [
        'conversation_id', 'proposed_by', 'campus_zone_id', 'parent_id',
        'proposed_at', 'status', 'notes', 'responded_at', 'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'proposed_at'  => 'datetime',
            'responded_at' => 'datetime',
            'accepted_at'  => 'datetime',
        ];
    }

    public function conversation(): BelongsTo  { return $this->belongsTo(Conversation::class); }
    public function proposedBy(): BelongsTo    { return $this->belongsTo(User::class, 'proposed_by'); }
    public function campusZone(): BelongsTo    { return $this->belongsTo(CampusZone::class); }
    public function parent(): BelongsTo        { return $this->belongsTo(MeetupProposal::class, 'parent_id'); }
    public function counterProposals(): HasMany { return $this->hasMany(MeetupProposal::class, 'parent_id'); }
    public function orders(): HasMany          { return $this->hasMany(Order::class); }

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isAccepted(): bool  { return $this->status === 'accepted'; }
}
