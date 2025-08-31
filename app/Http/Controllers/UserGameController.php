<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\CustomUserGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserGameController extends Controller
{
    // List all MyGames (system + custom)
    public function index()
    {
        $user = Auth::user();

        $systemGames = $user->games()->get()->map(fn($g) => [
            'id' => $g->id,
            'title' => $g->title,
            'description' => $g->description,
            'custom' => false,
        ]);

        $customGames = $user->customUserGames()->get()->map(fn($g) => [
            'id' => $g->id,
            'title' => $g->title,
            'description' => $g->description,
            'custom' => true,
        ]);

        $games = $systemGames->concat($customGames);

        return inertia('Games/MyGames', ['games' => $games]);
    }

    // Add system or custom game
    public function store(Request $request)
    {
        if ($request->custom) {
            // ✅ Custom game
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'rules' => 'nullable|string',
                'min_players' => 'nullable|integer|min:1',
                'max_players' => 'nullable|integer|min:1',
                'category' => 'nullable|string|max:255',
            ]);

            Auth::user()->customUserGames()->create($request->only([
                'title','description','rules','min_players','max_players','category'
            ]));

            return back()->with('success', 'Custom game added!');
        } else {
            // ✅ System game
            $request->validate([
                'game_id' => 'required|integer|exists:games,id',
            ]);

            Auth::user()->games()->syncWithoutDetaching($request->game_id);

            return back()->with('success', 'Game added to MyGames!');
        }
    }

    // Remove system or custom game
    public function destroy($id, Request $request)
    {
        $type = $request->query('type', 'system');

        if ($type === 'custom') {
            $game = Auth::user()->customUserGames()->findOrFail($id);
            $game->delete();
        } else {
            $game = Game::findOrFail($id);
            Auth::user()->games()->detach($game->id);
        }

        return response()->json(['message' => 'Game removed!']);
    }
}
