<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Group;
use App\Models\Submission;
use App\Models\User;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use FiveamCode\LaravelNotionApi\Query\Filters\FilterBag;
use FiveamCode\LaravelNotionApi\Query\Filters\Operators;
use \Notion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class TrackController extends Controller
{
    function view(Request $request, String $submission_id)
    {

        $submission = Submission::with('votes')->findOrFail($submission_id);
        $episode = Episode::findOrFail($submission->episode_id);
        $season = $episode->season;
        $group = Group::findOrFail($season->group_id);
        $group_users = $group->users;

        $adder = User::where('spotify_id', $submission->added_by)->get()[0];

        return Inertia::render('Track', [
            "track" => $submission,
            "adder" => $adder,
            "episode" => $episode,
            "season" => $season,
            "group" => $group,
            "group_users" => $group_users,
        ]);
    }

    function albumScore(Request $request, String $artist, String $album)
    {

        $users = [];

        $bag = new FilterBag(Operators::AND);
        $bag->addFilter(Filter::textFilter('Title', Operators::EQUALS, $album));
        $bag->addFilter(Filter::textFilter('Artist', Operators::EQUALS, explode(',',$artist)[0]));

        // Open Escher's rankings database
        /** @var Collection */
        $items = Notion::database("f0a3729e8f7746ae8545daed407789cd")->filterBy($bag)->limit(10)->query()->asCollection();
        // $items = \Notion::databases()->find("f0a3729e8f7746ae8545daed407789cd");

        if ($items->count() > 0) {
            /** @var Page */
            $row = $items[0];
            $users[] = [
                "score" => $row->getProperty('Score')->getContent(),
                "name" => "Escher",
                "url" => "https://escherwd.notion.site/f0a3729e8f7746ae8545daed407789cd?v=f79b920957174566ac1919329da8a52e"
            ];
        }

        return $users;
    }

    function artistImage(Request $request, String $track_id)
    {

        $user = User::all()->first();

        $user->refreshToken();

        $track_response = Http::withHeaders([
            "Authorization" => "Bearer " . $user->spotify_token,
        ])->acceptJson()->get('https://api.spotify.com/v1/tracks/' . $track_id)->json();

        $artist_id = $track_response["artists"][0]["id"];

        $artist_response = Http::withHeaders([
            "Authorization" => "Bearer " . $user->spotify_token,
        ])->acceptJson()->get('https://api.spotify.com/v1/artists/' . $artist_id)->json();

        return [
            'banner' => $artist_response["images"][0]["url"]
        ];

    }
}
