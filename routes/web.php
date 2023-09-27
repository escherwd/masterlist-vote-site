<?php

use App\Http\Controllers\Auth\SpotifyLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::id()) {
        return redirect("/dashboard");
    }

    return redirect("/login");
});

Route::middleware('auth')->group(function () {
    # Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    # Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    # Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'view'])->name('dashboard');
    Route::get('/history', function () {
        return "hi";
    })->name('history');
    Route::get('/stats', function () {
        return "hi";
    })->name('stats');
});


require __DIR__ . '/auth.php';
require __DIR__ . '/api.php';
