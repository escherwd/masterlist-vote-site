<?php

use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\VoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('/vote', [VoteController::class, 'store'])->name('vote.store');
    Route::post('/episode/{id}/refresh', [EpisodeController::class, 'refreshTracks'])->name('episode.refresh');
    Route::post('/episode/{id}/finish', [EpisodeController::class, 'finishEpisode'])->name('episode.finish');
});
