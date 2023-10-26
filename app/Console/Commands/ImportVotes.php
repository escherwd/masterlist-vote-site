<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Season;
use App\Models\Submission;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportVotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-votes {user?}';

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

        $onlyUser = $this->argument('user');

        $curr_season_id = null;
        $curr_episode_id = null;
        $season_num = null;
        $episode_num = null;
        $theme = null;

        $users = [
            'E' => User::find(1),
            'M' => User::find(2),
            'S' => User::find(3),
            'D' => User::find(4),
            'J' => User::find(5),
            'K' => User::find(6),
            'A' => User::find(7),
            'R' => User::find(8),
        ];
        $escher = $users['E'];
        $escher->refreshToken();

        $group_id = 1;
        $masterlist = "4bzrIMzx73vefmmmG5Mn7U";
        $submissions = "5dVgKuDflRmcLdcFGvzMdR";
        $bangers = "6p0ThoJ9blufSk9kGITN88";
        $vote_min = 4;

        $handle = fopen("import.txt", "r");
        $fails = fopen(($onlyUser != null) ? "import_fails_" . $onlyUser . ".txt" : "import_fails.txt", "w");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {

                $this->newLine(30);

                if (str_starts_with($line, "Season ")) {
                    fwrite($fails, $line);
                    // $this->info($line);
                    preg_match_all('/Season (\d+): Episode (\d+) - (.+)/m', $line, $parts, PREG_SET_ORDER, 0);
                    $season_num = intval($parts[0][1]);
                    $episode_num = intval($parts[0][2]);
                    $theme = trim($parts[0][3]);
                    // $curr_season = intval($season);
                    // $curr_episode = intval($episode);

                    // $this->info('S: ' . $season_num . ' E: ' . $episode_num . ' theme: ' . trim($theme));

                    // Create a new season
                    $season = Season::updateOrCreate([
                        "group_id" => $group_id,
                        "number" => $season_num,
                    ], [
                        "playlist_submissions" => $submissions,
                        "playlist_masterlist" => $masterlist,
                        "playlist_bangers" => $bangers,
                    ]);

                    $curr_season_id = $season->id;

                    $episode = Episode::updateOrCreate([
                        "season_id" => $season->id,
                        "number" => $episode_num,
                    ], [
                        "theme" => $theme,
                        "vote_min" => $vote_min,
                    ]);

                    $curr_episode_id = $episode->id;
                } else {
                    [$title, $voters, $submitter] = explode("\t", $line);
                    $submitter = trim($submitter);

                    if ($onlyUser != null && $submitter != $onlyUser) continue;

                    $this->line('[S: ' . $season_num . ', E: ' . $episode_num . '] ' . $theme);
                    $this->line('Voters: ' . $voters . ', Added by: ' . $submitter);
                    $this->info('Track: "' . $title . '"');
                    $response = Http::withHeaders([
                        "Authorization" => "Bearer " . $escher->spotify_token,
                    ])->acceptJson()->get('https://api.spotify.com/v1/search', [
                        "q" => $title,
                        "type" => "track",
                        "limit" => 9
                    ]);

                    // Gather Results
                    $results = $response->json();
                    // Show Options
                    $options = array_map(function ($track) {
                        return implode(", ", array_map(fn ($artist) => $artist["name"], $track["artists"])) . " - " . $track["name"];
                    }, $results["tracks"]["items"]);
                    $options[] = "None";
                    $choice = $this->choice('Please choose the correct option', $options);
                    if ($choice == "None") {
                        // Couldn't find it
                        fwrite($fails, $line);
                    } else {
                        // Found it
                        $index = array_search($choice, $options);
                        $item = $results["tracks"]["items"][$index];
                        $submission = Submission::updateOrCreate([
                            "episode_id" => $curr_episode_id,
                            "track_id" => $item["id"],
                        ], [
                            "season_id" => $curr_season_id,
                            "group_id" => $group_id,
                            "title" => $item["name"],
                            "artist" => implode(", ", array_map(fn ($artist) => $artist["name"], $item["artists"])),
                            "album" => $item["album"]["name"],
                            "album_id" => $item["album"]["id"],
                            "album_cover" => $item["album"]["images"][0]["url"],
                            "year" => (int) substr($item["album"]["release_date"], 0, 4),
                            "added_by" => $users[$submitter]->spotify_id,
                            "order" => $index,
                        ]);

                        // Add votes
                        $voters = array_map(function ($v) use ($users) {
                            return $users[$v];
                        }, str_split($voters));
                        foreach ($voters as $person) {
                            Vote::updateOrCreate([
                                "user_id" => $person->id,
                                "submission_id" => $submission->id,
                            ]);
                        }
                    }
                }
            }

            fclose($handle);
            fclose($fails);
        }
    }
}
