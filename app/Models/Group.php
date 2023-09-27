<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $guarded = [];

    // A group contains many seasons
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->as('user_groups')
            ->withTimestamps();
    }
}
