<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class EpisodeController extends Controller
{
    public function refreshTracks(Request $request, $episodeId) {
        Artisan::call('app:refresh-submissions', [ 'episode_id' => $episodeId ]);
    }

    public function finishEpisode(Request $request, $episodeId) {
        Artisan::call('app:finish-episode', [ 'episode_id' => $episodeId, 'finish_season' => $request->get('finish_season') ?? false ]);
        return to_route('dashboard');
    }

    public function update(Request $request, $episodeId) {
        $episode = Episode::findOrFail($episodeId);

        $episode->theme = $request->get('theme') ?? '';

        $episode->save();
    }
}
