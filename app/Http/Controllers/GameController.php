<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\CustomUserGame;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tag;



class GameController extends Controller
{
    // public function index()
    // {
    //     $games = Game::all();
    //     return Inertia::render('Games/Index', [
    //         'games' => $games,
    //     ]);
    // }


public function index()
{
    $games = Game::with('tags')->get(); // eager load tags
    $tags = Tag::all();

    return Inertia::render('Games/Index', [
        'games' => $games,
        'tags' => $tags,
    ]);
}

    public function show($id, $type)
    {
        $type = $type ?? 'system';

        if ($type === 'custom') {
            $game = CustomUserGame::where('id', $id)->firstOrFail();
        } else {
            // $game = Game::findOrFail($id);
            $game = Game::with('tags')->findOrFail($id);
        }

        return Inertia::render('Games/Show', [
            'game' => $game,
            'type' => $type,
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
