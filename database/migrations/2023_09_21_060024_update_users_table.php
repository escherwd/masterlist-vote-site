<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add Spotify info
        Schema::table('users', function (Blueprint $table) {
            $table->string('spotify_id')->unique();
            $table->text('spotify_token');
            $table->text('spotify_refresh_token');
            $table->string('spotify_avatar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
