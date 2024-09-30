<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class SpotifyLoginController extends Controller
{

    // Send user to the Spotify login page
    public function redirect(Request $request)
    {

        return Socialite::driver('spotify')->scopes(['playlist-read-private', 'playlist-read-collaborative', 'playlist-modify-private', 'playlist-modify-public'])->redirect();
    }

    // Handle authentication callback
    public function callback(Request $request)
    {
        $spotify_user = Socialite::driver('spotify')->user();

        $user = User::where('spotify_id', $spotify_user->id)->first();

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

    public function access(Request $request)
    {
        return Inertia::render('Auth/Access', [
            'error' => session('error'),
        ]);
    }

    public function submitAccess(Request $request)
    {

        $request->validate([
            'access_code' => ['required'],
        ]);

        $user = Auth::user();

        if (strtolower($request->access_code) == env('ACCESS_CODE')) {
            // Password is correct, verify the user
            $user->verified = true;
            $user->save();
            return redirect('dashboard');
        } else {
            return redirect()->route('access.show')->with('error', 'Incorrect');
        }

        
    }
}
