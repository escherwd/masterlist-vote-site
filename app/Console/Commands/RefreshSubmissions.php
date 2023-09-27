<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Group;
use App\Models\Season;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RefreshSubmissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-submissions {episode_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $episode_id = $this->argument('episode_id');

        $episode = Episode::find($episode_id);

        // Get the newest season
        $season = Season::find($episode->season_id);

        // Grab a Spotify token from a user
        /** @var User */
        $group = Group::find($season->group_id);
        $user = $group->users->first();
        $user->refreshToken();
        $token = $user->spotify_token;

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->acceptJson()->get('https://api.spotify.com/v1/playlists/' . $season->playlist_submissions . '/tracks');

        $results = $response->json();

        if (!isset($results['items'])) {
            throw "Items are missing in Spotify request. Perhaps the token is expired?";
        }

        $existingIds = Submission::where('episode_id', $episode_id)->pluck('id')->all();

        foreach ($results["items"] as $index => $item) {
            $track = Submission::updateOrCreate([
                "episode_id" => Episode::where('season_id', $season->id)->max('id'),
                "track_id" => $item["track"]["id"],
            ], [
                "title" => $item["track"]["name"],
                "artist" => implode(", ",array_map(fn ($artist) => $artist["name"], $item["track"]["artists"])),
                "album" => $item["track"]["album"]["name"],
                "album_id" => $item["track"]["album"]["id"],
                "album_cover" => $item["track"]["album"]["images"][0]["url"],
                "year" => (int) substr($item["track"]["album"]["release_date"], 0, 4),
                "added_by" => $item["added_by"]["id"],
                "order" => $index,
            ]);

            $existingIds = array_filter($existingIds, static function ($id) use ($track) {
                return $id !== $track->id;
            });
        }

        // Clean up deleted tracks
        Submission::whereIn('id', $existingIds)->delete();
    }
}
