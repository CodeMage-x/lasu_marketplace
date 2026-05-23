<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampusZone extends Model
{
    protected $fillable = ['name', 'description', 'latitude', 'longitude', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'latitude' => 'float', 'longitude' => 'float'];
    }

    public function listings(): HasMany         { return $this->hasMany(Listing::class); }
    public function meetupProposals(): HasMany   { return $this->hasMany(MeetupProposal::class); }
}
