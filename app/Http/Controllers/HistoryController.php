<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function view(Request $request, $season_id = null)
    {

        /** @var User */
        $user = Auth::user();

        $group = $user->groups()->first();
        $group_users = $group->users;
        $seasons = $group->seasons;
        $episodes = [];

        if ($season_id) {
            $season = Season::findOrFail($season_id);
            $episodes = $season->episodes()->with(['submissions.votes','season'])->get();
        } else {
            $episodes = Episode::whereIn("season_id",$seasons->pluck('id'))->with(['submissions.votes','season'])->get();
            // dd(array_map(function ($s) { return $s->id; }, $seasons));
        }

        return Inertia::render('History', [
            "seasons" => $seasons,
            "season_id" => $season_id,
            "episodes" => $episodes,
            "group_users" => $group_users,
            "group" => $group
        ]);
    }
}
