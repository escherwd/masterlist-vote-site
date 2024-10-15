<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Season;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StatsController extends Controller
{
    function view(Request $request, $season_id = null, $episode_id = null)
    {

        /** @var User */
        $user = Auth::user();

        $reqKey = "stats-$season_id-$episode_id";

        // Check if this request is cached
        $value = Cache::get($reqKey);
        if ($value) {
            // Render straight from cache
            return Inertia::render('Stats', $value);
        }

        $group = $user->groups()->first();
        $group_users = $group->users;
        $seasons = $group->seasons;
        $episodes = null;
        $tracks = [];
        $top_artists = [];
        $top_albums = [];
        $top_artist_limit = 12;
        $top_albums_limit = 9;

        if ($season_id) {
            $season = Season::findOrFail($season_id);
            $episodes = $season->episodes;

            if ($episode_id) {
                // just tracks for a single episode
                $tracks = Submission::where("episode_id", $episode_id)->with('votes')->get();
                // TODO: these need to specific to the group
                $top_artists = DB::select('SELECT artist, COUNT(*) FROM submissions WHERE episode_id = ? GROUP BY artist HAVING COUNT(*) >= 2 ORDER BY COUNT(*) DESC LIMIT ?', [$episode_id, $top_artist_limit]);
                $top_albums = DB::select('SELECT artist, album, album_cover, COUNT(*) FROM submissions WHERE episode_id = ? GROUP BY album HAVING COUNT(*) >= 2 ORDER BY COUNT(*) DESC LIMIT ?', [$episode_id, $top_albums_limit]);
            } else {
                // all tracks for a whole season
                $tracks = Submission::where("season_id", $season_id)->with('votes')->get();
                $top_artists = DB::select('SELECT artist, COUNT(*) FROM submissions WHERE season_id = ? GROUP BY artist HAVING COUNT(*) >= 2 ORDER BY COUNT(*) DESC LIMIT ?', [$season_id, $top_artist_limit]);
                $top_albums = DB::select('SELECT artist, album, album_cover, COUNT(*) FROM submissions WHERE season_id = ? GROUP BY album HAVING COUNT(*) >= 2 ORDER BY COUNT(*) DESC LIMIT ?', [$season_id, $top_albums_limit]);
            }
        } else {
            // all tracks for the group
            $tracks = Submission::where("group_id", $group->id)->with('votes')->get();
            $top_artists = DB::select('SELECT artist, COUNT(*) FROM submissions GROUP BY artist HAVING COUNT(*) >= 2 ORDER BY COUNT(*) DESC LIMIT ?', [$top_artist_limit]);
            $top_albums = DB::select('SELECT artist, album, album_cover, COUNT(*) FROM submissions GROUP BY album HAVING COUNT(*) >= 2 ORDER BY COUNT(*) DESC LIMIT ?', [$top_albums_limit]);
        }

        $allData = [
            "seasons" => $seasons,
            "season_id" => $season_id,
            "episodes" => $episodes,
            "episode_id" => $episode_id,
            "group_users" => $group_users,
            "group" => $group,
            "tracks" => $tracks,
            "top_artists" => $top_artists,
            "top_albums" => $top_albums,
        ];

        // Cache the value for the next 3 days
        Cache::add($reqKey, $allData, now()->addDays(3));

        return Inertia::render('Stats', $allData);
    }
}
