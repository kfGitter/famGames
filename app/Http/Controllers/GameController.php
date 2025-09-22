<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\CustomUserGame;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GameController extends Controller
{
    /**
     * List all system games with tags, plus all tags for filtering.
     */
    public function index()
    {
        $games = Game::with('tags')->get(); // eager load tags
        $tags = Tag::all();

        return Inertia::render('Games/Index', [
            'games' => $games,
            'tags' => $tags,
        ]);
    }

    /**
     * Show details of a specific game (system or custom).
     */
    public function show(int $id, string $type = 'system')
    {
        $game = $this->getGameByType($id, $type);

        return Inertia::render('Games/Show', [
            'game' => $game,
            'type' => $type,
        ]);
    }

    /**
     * Show all games added by the authenticated user.
     */
    public function myGames()
    {
        $user = Auth::user();
        $myGames = $user->games()->with('tags')->get(); // include tags for consistency

        return Inertia::render('Games/MyGames', [
            'games' => $myGames,
        ]);
    }

    /**
     * Add a system game to the authenticated user's collection.
     */
    public function addToMyGames(Game $game)
    {
        $user = Auth::user();

        if (! $user->games()->where('game_id', $game->id)->exists()) {
            $user->games()->attach($game->id);
        }

        return redirect()->back()->with('success', 'Game added to your collection!');
    }

    /**
     * Helper: fetch game by type (system/custom).
     */
    private function getGameByType(int $id, string $type)
    {
        return $type === 'custom'
            ? CustomUserGame::where('id', $id)->firstOrFail()
            : Game::with('tags')->findOrFail($id);
    }
}
