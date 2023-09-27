<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Season>
 */
class SeasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "playlist_submissions" => "5dVgKuDflRmcLdcFGvzMdR",
            "playlist_masterlist" => "4bzrIMzx73vefmmmG5Mn7U",
            "playlist_bangers" => "6p0ThoJ9blufSk9kGITN88",
        ];
    }
}
