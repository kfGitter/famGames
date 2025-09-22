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

        $systemGames = $user->games()->with('tags')->get()->map(fn($game) => [
            'id' => $game->id,
            'title' => $game->title,
            'description' => $game->description,
            'tags' => $game->tags,
            'custom' => false,
            'is_favorite' => (bool) ($game->pivot->is_favorite ?? false),
        ]);

        $customGames = $user->customUserGames()->with('tags')->get()->map(fn($game) => [
            'id' => $game->id,
            'title' => $game->title,
            'description' => $game->description,
            'tags' => $game->tags,
            'custom' => true,
            'is_favorite' => (bool) ($game->is_favorite ?? false),
        ]);

        $games = $systemGames->concat($customGames);
        $members = $user->family?->members ?? [];

        return inertia('Games/MyGames', compact('games', 'members'));
    }

    // Toggle favorite status
    public function toggleFavorite(string $type, int $id)
    {
        $user = Auth::user();
        $isFavorite = false;

        if ($type === 'system') {
            $game = $user->games()->where('game_id', $id)->firstOrFail();
            $isFavorite = ! $game->pivot->is_favorite;
            $user->games()->updateExistingPivot($id, ['is_favorite' => $isFavorite]);
        } elseif ($type === 'custom') {
            $customGame = $user->customUserGames()->findOrFail($id);
            $isFavorite = ! $customGame->is_favorite;
            $customGame->update(['is_favorite' => $isFavorite]);
        }

        return response()->json(['success' => true, 'is_favorite' => $isFavorite]);
    }

    // Add system or custom game
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($request->boolean('custom')) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'rules' => 'nullable|string',
                'min_players' => 'nullable|integer|min:1',
                'max_players' => 'nullable|integer|min:1',
                'category' => 'nullable|string|max:255',
                'tags' => 'nullable|array',
                'tags.*' => 'exists:tags,id',
            ]);

            $customGame = $user->customUserGames()->create($request->only([
                'title', 'description', 'rules', 'min_players', 'max_players', 'category'
            ]));

            if (!empty($request->tags)) {
                $customGame->tags()->sync($request->tags);
            }

            return back()->with('success', 'Custom game added!');
        }

        // System game
        $request->validate([
            'game_id' => 'required|integer|exists:games,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $user->games()->syncWithoutDetaching($request->game_id);

        if (!empty($request->tags)) {
            $game = Game::find($request->game_id);
            $game->tags()->sync($request->tags);
        }

        return back()->with('success', 'Game added to MyGames!');
    }

    // Remove system or custom game
    public function destroy(int $id, Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'system');

        if ($type === 'custom') {
            $user->customUserGames()->findOrFail($id)->delete();
        } else {
            $user->games()->detach($id);
        }

        return response()->json(['message' => 'Game removed!']);
    }
}
