<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Season;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StatsController extends Controller
{
    function view(Request $request, $season_id = null, $episode_id = null) {

        /** @var User */
        $user = Auth::user();

        $group = $user->groups()->first();
        $group_users = $group->users;
        $seasons = $group->seasons;
        $episodes = null;
        $tracks = [];

        if ($season_id) {
            $season = Season::findOrFail($season_id);
            $episodes = $season->episodes;

            if ($episode_id) {
                // just tracks for a single episode
                $tracks = Submission::where("episode_id",$episode_id)->with('votes')->get();
            } else {
                // all tracks for a whole season
                $tracks = Submission::where("season_id",$season_id)->with('votes')->get();
            }
        } else {
            // all tracks for the group
            $tracks = Submission::where("group_id",$group->id)->with('votes')->get();
        }

        return Inertia::render('Stats', [
            "seasons" => $seasons,
            "season_id" => $season_id,
            "episodes" => $episodes,
            "episode_id" => $episode_id,
            "group_users" => $group_users,
            "group" => $group,
            "tracks" => $tracks
        ]);
    }
}
