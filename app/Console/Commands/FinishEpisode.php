<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Group;
use App\Models\Season;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FinishEpisode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:finish-episode {episode_id} {finish_season}';

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
        // Organize command arguments
        $episode_id = $this->argument('episode_id');
        $finish_season = $this->argument('finish_season');

        // Collect all the objects
        $episode = Episode::find($episode_id);
        $tracks = $episode->submissions()->with('votes')->get();
        $season = Season::find($episode->season_id);
        $group = Group::find($season->group_id);
        $members = $group->users;
        $user = $members[0];
        $user->refreshToken();

        // Determine thresholds
        $pass_threshold = $episode->vote_min;
        $banger_threshold = count($members);

        // Collect ids
        $ids_banger = [];
        $ids_masterlist = [];
        $ids_all = [];
        foreach ($tracks as $track) {
            $votes = count($track->votes);
            if ($votes >= $pass_threshold) {
                $ids_masterlist[] = "spotify:track:" . $track->track_id;
            }
            if ($votes >= $banger_threshold) {
                $ids_banger[] = "spotify:track:" . $track->track_id;
            }
            $ids_all[] = "spotify:track:" . $track->track_id;
        }

        // Add songs to master list
        if (count($ids_masterlist) > 0 && env("SPOTIFY_DO_MAKE_CHANGES", false)) {
            $this->addToPlaylist($user->spotify_token, $season->playlist_masterlist, $ids_masterlist);
        }

        // Add songs to certified bangers
        if (count($ids_banger) > 0 && env("SPOTIFY_DO_MAKE_CHANGES", false)) {
            $this->addToPlaylist($user->spotify_token, $season->playlist_bangers, $ids_banger);
        }

        // Remove tracks from submissions list
        if (count($ids_all) > 0 && env("SPOTIFY_DO_MAKE_CHANGES", false)) {
            $this->removeFromPlaylist($user->spotify_token, $season->playlist_submissions, $ids_all);
        }

        $new_season = null;
        if ($finish_season) {
            $new_season = new Season;
            $new_season->group_id = $group->id;
            $new_season->number = $season->number + 1;
            $new_season->playlist_submissions = $season->playlist_submissions;
            $new_season->playlist_masterlist = $season->playlist_masterlist;
            $new_season->playlist_bangers = $season->playlist_bangers;
            $new_season->save();
        }

        // Create new episode
        $new_episode = new Episode;
        $new_episode->season_id = isset($new_season) ? $new_season->id : $season->id;
        $new_episode->number = isset($new_season) ? 1 : $episode->number + 1;
        $new_episode->theme = "";
        $new_episode->vote_min = $group->vote_min;
        $new_episode->save();
    }

    function addToPlaylist(string $token, string $id, array $track_ids) {
        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->acceptJson()->post('https://api.spotify.com/v1/playlists/' . $id . '/tracks', [
            "uris" => $track_ids
        ])->json();

        // Ensure no error
        if (isset($response["error"])) {
            throw $response["error"];
        }
    }

    function removeFromPlaylist(string $token, string $id, array $track_ids) {
        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->acceptJson()->delete('https://api.spotify.com/v1/playlists/' . $id . '/tracks', [
            "uris" => $track_ids
        ])->json();

        // Ensure no error
        if (isset($response["error"])) {
            throw $response["error"];
        }
    }
}
