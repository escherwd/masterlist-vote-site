<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class EpisodeController extends Controller
{
    public function refreshTracks(Request $request, $episodeId) {
        Artisan::call('app:refresh-submissions', [ 'episode_id' => $episodeId ]);
    }
}
