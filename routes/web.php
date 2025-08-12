<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserGameController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'auth' => ['user' => Auth::user()],
    ]);
})->middleware(['auth'])->name('dashboard');



// Route to view all games in the library
Route::get('/games', function () {
    return Inertia::render('Games/Index', [
        'auth' => ['user' => Auth::user()],
    ]);
})->name('games')->middleware(['auth']);

Route::get('/games', [GameController::class, 'index'])
    ->middleware('auth')
    ->name('games');

// Show a specific game in the games library
Route::get('/games/{game}', [GameController::class, 'show'])
    ->middleware('auth')
    ->name('games.show');

// View all My Games
Route::get('/my-games', [MyGamesController::class, 'index'])->name('my-games.index');

// Add a game (you already have this)
Route::post('/my-games/{game}', [MyGamesController::class, 'store'])->name('my-games.store');

// Remove a game
Route::delete('/my-games/{game}', [MyGamesController::class, 'destroy'])->name('my-games.destroy');


// User Game Controller for handling user-specific game actions
Route::post('/my-games/{game}', [UserGameController::class, 'store'])
    ->middleware('auth')
    ->name('my-games.store');

// Game Controller for handling game-related actions
Route::middleware('auth')->group(function () {
    Route::get('/my-games', [GameController::class, 'myGames'])->name('my-games.index');
    Route::post('/my-games/{game}', [GameController::class, 'addToMyGames'])->name('my-games.store');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
