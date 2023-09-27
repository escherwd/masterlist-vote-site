<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Group;
use App\Models\Season;
use Illuminate\Console\Command;

class FinishEpisode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:finish-episode {$episode_id}';

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
        $tracks = $episode->submissions;
        $votes = $episode->votes();
        $season = Season::find($episode->season_id);
        $group = Group::find($season->group_id);
        $members = $group->users;

        $pass_threshold = $episode->vote_min;


        // Add songs to master list

        // Add songs to certified bangers

        // Clear master list

        // Create new episode
    }
}
