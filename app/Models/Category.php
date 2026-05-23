<?php
// ── Category ──────────────────────────────────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'is_active', 'sort_order'];

    protected function casts(): array { return ['is_active' => 'boolean']; }

    public function listings(): HasMany { return $this->hasMany(Listing::class); }
}
