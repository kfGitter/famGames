<?php 

use App\Http\Controllers\{
    GameController,
    UserGameController,
    UserController,
    GameSessionController,
    FamilyMemberController,
    LeaderboardsController,
    AchievementController,
    DashboardController
};
use App\Models\Tag;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Public home page
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Games
    Route::get('/games', [GameController::class, 'index'])->name('games');
    Route::get('/games/{id}/{type?}', [GameController::class, 'show'])->name('games.show');

    // My Games
    Route::get('/my-games', [UserGameController::class, 'index'])->name('my-games.index');
    Route::post('/my-games', [UserGameController::class, 'store'])->name('my-games.store');
    Route::delete('/my-games/{id}', [UserGameController::class, 'destroy'])->name('my-games.destroy');
    Route::post('/my-games/{type}/{id}/favorite', [UserGameController::class, 'toggleFavorite'])->name('my-games.favorite');

    Route::get('/my-games/create', function () {
        $tags = Tag::all();
        return inertia('Games/AddCustomGame', ['tags' => $tags]);
    })->name('my-games.create');

    // Game Sessions
    Route::get('/start-game/{id}/{type?}', [GameSessionController::class, 'create'])->name('game.start');
    Route::post('/start-game/{id}/{type?}', [GameSessionController::class, 'store'])->name('game.store');
    Route::get('/game-session/{gameSession}/scores', [GameSessionController::class, 'enterScores'])->name('game.session.scores.enter');
    Route::post('/game-session/{gameSession}/scores', [GameSessionController::class, 'saveScores'])->name('game.session.scores.save');

    // Family Members
    Route::get('/family-members', [FamilyMemberController::class, 'index'])->name('family-members.index');
    Route::get('/family-members/create', [FamilyMemberController::class, 'create'])->name('family-members.create');
    Route::post('/family-members', [FamilyMemberController::class, 'store'])->name('family-members.store');
    Route::get('/family-members/{member}', [FamilyMemberController::class, 'show'])->name('family-members.show');
    Route::delete('/family-members/{member}', [FamilyMemberController::class, 'destroy'])->name('family-members.destroy');

    // User avatar
    Route::post('/user/avatar', [UserController::class, 'updateAvatar'])->name('user.avatar');

    // Dashboard challenges
    Route::get('/challenges/manage', [DashboardController::class, 'manage'])->name('challenges.manage');
    Route::post('/challenges', [DashboardController::class, 'store'])->name('challenges.store');
    Route::post('/challenges/activate', [DashboardController::class, 'activate'])->name('challenges.activate');
    Route::delete('/challenges/{familyChallenge}', [DashboardController::class, 'deactivate'])->name('challenges.deactivate');

});

// Verified routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/leaderboards', [LeaderboardsController::class, 'index'])->name('leaderboards.index');
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/achievements/{member}', [AchievementController::class, 'show'])->name('achievements.show');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
