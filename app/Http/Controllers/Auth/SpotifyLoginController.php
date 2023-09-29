<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SpotifyLoginController extends Controller {

    // Send user to the Spotify login page
    public function redirect(Request $request) {

        return Socialite::driver('spotify')->redirect();
    }

    // Handle authentication callback
    public function callback(Request $request) {
        $spotify_user = Socialite::driver('spotify')->user();

        $user = User::updateOrCreate([
            'spotify_id' => $spotify_user->id,
        ], [
            'name' => $spotify_user->name,
            'spotify_token' => $spotify_user->token,
            'spotify_refresh_token' => $spotify_user->refreshToken,
            'spotify_avatar' => $spotify_user->avatar,
            'spotify_token_expire' => time() + 3600 // one hour
        ]);
     
        Auth::login($user);

        // Attach to first group for now
        Group::first()->users()->sync($user, false);

        return redirect('/');
    }

}