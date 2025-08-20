<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserGameController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


use App\Http\Controllers\GameSessionController;
use App\Http\Controllers\FamilyMemberController;
use Illuminate\Support\Facades\Auth;


Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'auth' => ['user' => Auth::user()],
        ]);
    })->name('dashboard');

    // Games index & show
    Route::get('/games', [GameController::class, 'index'])->name('games');
    Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');

    // My Games (list, add, remove)
    Route::get('/my-games', [UserGameController::class, 'index'])->name('my-games.index');
    Route::post('/my-games/{game}', [UserGameController::class, 'store'])->name('my-games.store');
    Route::delete('/my-games/{game}', [UserGameController::class, 'destroy'])->name('my-games.destroy');

   Route::middleware('auth')->group(function () {
    Route::get('/start-game/{game}', [GameSessionController::class, 'create'])->name('game.start');
    Route::post('/start-game/{game}', [GameSessionController::class, 'store'])->name('game.store');
    Route::get('/game-session/{gameSession}/scores', [GameSessionController::class, 'enterScores'])->name('game.session.scores.enter');
    Route::post('/game-session/{gameSession}/scores', [GameSessionController::class, 'saveScores'])->name('game.session.scores.save');
});

    // Family members
    Route::get('/family-members', [FamilyMemberController::class, 'index'])->name('family-members.index');
    Route::get('/family-members/create', [FamilyMemberController::class, 'create'])->name('family-members.create');
    Route::post('/family-members', [FamilyMemberController::class, 'store'])->name('family-members.store');
    Route::get('/family-members/{member}', [FamilyMemberController::class, 'show'])->name('family-members.show');
    Route::delete('/family-members/{member}', [FamilyMemberController::class, 'destroy'])->name('family-members.destroy');
});

use App\Http\Controllers\LeaderboardsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/leaderboards', [LeaderboardsController::class, 'index'])->name('leaderboards.index');
});

use App\Http\Controllers\AchievementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/achievements/{member}', [AchievementController::class, 'show'])->name('achievements.show');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
