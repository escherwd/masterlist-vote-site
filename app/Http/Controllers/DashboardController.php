<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Season;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function view(Request $request) {

        /** @var User */
        $user = Auth::user();
        
        $user->refreshToken();

        /** @var Group */
        $group = $user->groups()->first();
        /** @var User[] */
        $group_users = $group->users()->get();
        /** @var Season */
        $season = $group->seasons()->orderBy('id','desc')->first();
        /** @var Episode */
        $episode = $season->episodes()->orderBy('id','desc')->first();
        /** @var Submission[] */
        $submissions = $episode->submissions()->orderBy('order', 'asc')->get();


        return Inertia::render('Dashboard', [
            "group" => $group,
            "group_users" => $group_users,
            "season" => $season,
            "episode" => $episode,
            "submissions" => $submissions,
            "votes_default" => $episode->votes(),
        ]);
    }
}
