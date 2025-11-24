<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PlatformController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', [GameController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard/games-table', [GameController::class, 'gamesTable'])->name('dashboard.games-table');

Route::middleware(['auth', 'verified'])->group(function () {
        // Game Routes
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::post('/games', [GameController::class, 'store'])->name('games.store');
    Route::get('/games/{game}/edit', [GameController::class, 'edit'])->name('games.edit');
    Route::put('/games/{game}', [GameController::class, 'update'])->name('games.update');
    Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');
    
        // Platform Routes
    Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');
    Route::post('/platforms', [PlatformController::class, 'store'])->name('platforms.store');
    Route::put('/platforms/{platform}', [PlatformController::class, 'update'])->name('platforms.update');
    Route::delete('/platforms/{platform}', [PlatformController::class, 'destroy'])->name('platforms.destroy');
    });

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

});

