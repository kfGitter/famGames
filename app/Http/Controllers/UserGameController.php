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

        $systemGames = $user->games()->with('tags')->get()->map(fn($g) => [
            'id' => $g->id,
            'title' => $g->title,
            'description' => $g->description,
            'tags' => $g->tags,
            'custom' => false,
        ]);

        $customGames = $user->customUserGames()->with('tags')->get()->map(fn($g) => [
            'id' => $g->id,
            'title' => $g->title,
            'description' => $g->description,
            'tags' => $g->tags,
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
                'tags' => 'nullable|array',
                'tags.*' => 'exists:tags,id',
            ]);

            $customGame = Auth::user()->customUserGames()->create($request->only([
                'title',
                'description',
                'rules',
                'min_players',
                'max_players',
                'category'
            ]));

            if (!empty($request->tags)) {
                $customGame->tags()->sync($request->tags);
            }

            return back()->with('success', 'Custom game added!');

        } else {
            // ✅ System game
            $request->validate([
                'game_id' => 'required|integer|exists:games,id',
                'tags' => 'nullable|array',
                'tags.*' => 'exists:tags,id',

            ]);

            Auth::user()->games()->syncWithoutDetaching($request->game_id);

            if (!empty($request->tags)) {
                $game = Game::find($request->game_id);
                $game->tags()->sync($request->tags);
            }

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
