<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Episode;
use App\Models\Group;
use App\Models\Season;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    static $ep = 1;

    public function run(): void
    {

        // Create a user
        $user = User::factory()->createOne([
            "name" => env('SPOTIFY_DEFAULT_NAME'),
            "spotify_id" => env('SPOTIFY_DEFAULT_ID'),
            "spotify_token" => env('SPOTIFY_DEFAULT_TOKEN'),
            "spotify_refresh_token" => env('SPOTIFY_DEFAULT_REFRESH_TOKEN'),
            "spotify_avatar" => env('SPOTIFY_DEFAULT_AVATAR'),
        ]);

        $group = Group::factory()->createOne();

        // Register the user to the group
        $user->groups()->attach($group);

        Season::factory(1)->create([
            "number" => 1,
            "group_id" => Group::first()->id,
        ]);

        Episode::factory(5)->create([
            "season_id" => Season::first()->id,
        ]);

        // Fetch real spotify data
        Artisan::call('app:refresh-submissions', [
            "group-id" => $group->id,
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
