<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class GameController extends Controller
{
    public function index()
    {
        $games = Game::all();
        return Inertia::render('Games/Index', [
            'games' => $games,
        ]);
    }

    public function show(Game $game)
    {
        return Inertia::render('Games/Show', [
            'game' => $game,
        ]);
    }

    public function myGames()
    {
        $user = Auth::user();
        $myGames = $user->games()->get();

        return Inertia::render('Games/MyGames', [
            'games' => $myGames,
        ]);
    }

    public function addToMyGames(Game $game)
    {
        $user = Auth::user();

        if (!$user->games()->where('game_id', $game->id)->exists()) {
            $user->games()->attach($game->id);
        }

        return redirect()->back()->with('success', 'Game added to your collection!');
    }
}
