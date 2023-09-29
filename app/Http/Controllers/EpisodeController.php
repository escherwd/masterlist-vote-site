<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class EpisodeController extends Controller
{
    public function refreshTracks(Request $request, $episodeId) {
        Artisan::call('app:refresh-submissions', [ 'episode_id' => $episodeId ]);
    }

    public function finishEpisode(Request $request, $episodeId) {
        Artisan::call('app:finish-episode', [ 'episode_id' => $episodeId ]);
    }
}
