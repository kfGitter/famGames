<?php

namespace App\Http\Controllers;


use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class UserGameController extends Controller
{
    public function store(Game $game): RedirectResponse
    {
        Auth::user()->games()->syncWithoutDetaching($game->id);
        return back()->with('message', 'Game added to your library!');
    }

    public function destroy(Game $game)
{
    $user = auth()->user();

    if (! $user->games()->where('game_id', $game->id)->exists()) {
        return response()->json(['message' => 'Game not found in your library.'], 404);
    }

    $user->games()->detach($game->id);

    return response()->json(['message' => 'Game removed from your library.']);
}

public function index()
{
    $games = auth()->user()->games()->get();

    return inertia('Games/MyGames', [
        'games' => $games
    ]);
}


}
