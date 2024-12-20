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
        $seasons = $group->seasons()->orderBy('number', 'asc')->get();
        $episodes = [];

        // dump($request->term);
        $query = $request->query('q');
        Episode::$searchQuery = $query;
        // if ($query) {
        //     dump($query);
        // }

        if ($season_id) {
            $season = Season::findOrFail($season_id);

            $episodes = $season->episodes()->orderBy('number', 'asc')->with(['submissions.votes', 'season'])->get();
            // dump($episodes);
            $episodes = $episodes->filter(function ($item) {
                return $item->submissions->count() > 0;
            })->values();
        } else {
            $episodes = Episode::whereIn("season_id", $seasons->pluck('id'))->orderBy('number', 'desc')->with(['submissions.votes', 'season'])->get()->toArray();
            // dd(array_map(function ($s) { return $s->id; }, $seasons));
            usort($episodes, function ($a, $b) {
                return $a["number"] - $b["number"];
            });
            usort($episodes, function ($a, $b) {
                return $a["season"]["number"] - $b["season"]["number"];
            });
            // Remove empty episodes (happens when searching)
            $episodes = array_filter($episodes, function ($item) {
                return sizeof($item["submissions"]) > 0;
            });
        }



        return Inertia::render('History', [
            "seasons" => $seasons,
            "season_id" => $season_id,
            "episodes" => $episodes,
            "group_users" => $group_users,
            "group" => $group,
            "query" => $query
        ]);
    }
}
