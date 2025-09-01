<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserGameController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Tag;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


use App\Http\Controllers\GameSessionController;
use App\Http\Controllers\FamilyMemberController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LeaderboardsController;
use App\Http\Controllers\AchievementController;



Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'auth' => ['user' => Auth::user()],
        ]);
    })->name('dashboard');

    // Games index & show
    Route::get('/games', [GameController::class, 'index'])->name('games');
    // Route::get('/games', [GameController::class, 'index'])->name('games.index')->middleware('auth');

    Route::get('/games/{id}/{type?}', [GameController::class, 'show'])->name('games.show');
});


Route::middleware('auth')->group(function () {
    Route::get('/my-games', [UserGameController::class, 'index'])->name('my-games.index');

    //library games
    Route::post('/my-games', [UserGameController::class, 'store'])->name('my-games.store');
    Route::delete('/my-games/{id}', [UserGameController::class, 'destroy'])->name('my-games.destroy');

    // Toggle favorite status
    Route::post('/my-games/{type}/{id}/favorite', [UserGameController::class, 'toggleFavorite'])
        ->name('my-games.favorite');



    Route::get('/my-games/create', function () {
        $tags = Tag::all(); // Fetch all available tags

        return inertia('Games/AddCustomGame', [
            'tags' => $tags
        ]);
    })->name('my-games.create');

    //    Route::middleware('auth')->group(function () {
    //     Route::get('/start-game/{game}', [GameSessionController::class, 'create'])->name('game.start');
    //     Route::post('/start-game/{game}', [GameSessionController::class, 'store'])->name('game.store');
    //     Route::get('/game-session/{gameSession}/scores', [GameSessionController::class, 'enterScores'])->name('game.session.scores.enter');
    //     Route::post('/game-session/{gameSession}/scores', [GameSessionController::class, 'saveScores'])->name('game.session.scores.save');
    // });

    Route::middleware('auth')->group(function () {
        // Start a game (system or custom)
        Route::get('/start-game/{id}/{type?}', [GameSessionController::class, 'create'])->name('game.start');
        Route::post('/start-game/{id}/{type?}', [GameSessionController::class, 'store'])->name('game.store');

        // Enter/save scores (these usually relate to a game session, not system/custom distinction)
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


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/leaderboards', [LeaderboardsController::class, 'index'])->name('leaderboards.index');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/achievements/{member}', [AchievementController::class, 'show'])->name('achievements.show');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
