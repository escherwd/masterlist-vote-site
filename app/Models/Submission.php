<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Submission extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'votes')
            ->as('votes')
            ->withTimestamps();
    }
}
