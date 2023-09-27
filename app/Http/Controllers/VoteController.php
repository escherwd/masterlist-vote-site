<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function store(Request $request) {
        
        $request->validate([
            "submission_id" => "required",
            "is_vote" => "required",
        ]);

        $submission_id = $request->get("submission_id");
        $is_voted = $request->get("is_vote");

        /** @var User */
        $user = Auth::user();

        if ($is_voted) {
            $user->votes()->attach($submission_id);
        } else {
            $user->votes()->detach($submission_id);
        }
        
    }
}
