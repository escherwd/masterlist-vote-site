<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SpotifyLoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;



// oauth
Route::get('/auth/redirect', [SpotifyLoginController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/callback', [SpotifyLoginController::class, 'callback'])->name('auth.callback');

// new accounts must enter access code
Route::get('/access', [SpotifyLoginController::class, 'callback'])->name('auth.callback');

Route::middleware('guest')->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

});

Route::middleware('auth')->group(function () {

    Route::get('access', [SpotifyLoginController::class, 'access'])->name('access.show');
    Route::post('access', [SpotifyLoginController::class, 'submitAccess'])->name('access.submit');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
