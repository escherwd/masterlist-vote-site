<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Episode extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    // A list of all the votes submitted for this episode
    public function votes()
    {
        $trackIds = Submission::where('episode_id', $this->id)->pluck('id')->all();
        $votes = Vote::whereIn('submission_id', $trackIds)->get();

        // Organize by [<track_id> => [<user_id>, <user_id>, <user_id>]]
        
        $grouped = [];
        foreach ($trackIds as $trackId) {
            // Populate all tracks with empty lists
            $grouped[$trackId] = [];
        }
        foreach ($votes as $vote) {
            // Add all the votes
            $grouped[$vote->submission_id][] = $vote->user_id;
        }

        return $grouped;
    }

}
