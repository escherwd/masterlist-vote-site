<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'spotify_id',
        'spotify_token',
        'spotify_refresh_token',
        'spotify_avatar',
        'verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'spotify_token',
        'spotify_refresh_token',
        'remember_token',
        'spotify_token_expire',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->as('user_groups')
            ->withTimestamps();
    }

    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(Submission::class, 'votes')
            ->as('votes')
            ->withTimestamps();
    }

    public function refreshToken()
    {

        // Token is not expired yet.
        if (time() < ($this->spotify_token_expire ?? 0)) return;

        $data = Http::withHeader("Authorization", "Basic " . base64_encode(env("SPOTIFY_CLIENT_ID") . ":" . env("SPOTIFY_CLIENT_SECRET")))
            ->asForm()
            ->post("https://accounts.spotify.com/api/token", [
                "grant_type" => 'refresh_token',
                "refresh_token" => $this->spotify_refresh_token
            ])->json();

        $this->spotify_token_expire = time() + $data["expires_in"];
        $this->spotify_token = $data['access_token'];
        $this->save();
    }
}
