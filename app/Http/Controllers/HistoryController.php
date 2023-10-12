<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function view(Request $request)
    {

        /** @var User */
        $user = Auth::user();

        $group = $user->groups()->first();
        $seasons = $group->seasons;

        return Inertia::render('History', [
            "seasons" => $seasons
        ]);
    }
}
