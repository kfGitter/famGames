<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameSession; // import this model
use App\Models\GameScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GameSessionController extends Controller
{
    // Show page to start a new session for a game (select players)
    public function create(Game $game)
    {
        $user = Auth::user();

    // If user has no family or no members in their family
    if (! $user->family || $user->family->members->isEmpty()) {
        return redirect()
            ->route('family-members.index')
            ->with('error', 'Add members in your family to start playing!');
    }

    $members = $user->family->members()->orderBy('name')->get();

    return Inertia::render('Games/Start', [
        'game' => $game,
        'members' => $members,
    ]);
    }

    // Create new session for the game with selected players
    public function store(Request $request, Game $game)
    {
        $messages = [
            'players.min' => 'You must select at least 2 players to start a game.',
        ];

        $validated = $request->validate([
            'players' => 'required|array|min:2',
            'players.*' => 'exists:family_members,id',
        ], $messages);

        $gameSession = $game->sessions()->create([
            'user_id' => Auth::id(),
            'status' => 'in_progress',
        ]);

        $gameSession->players()->attach($validated['players']);

        // Redirect to score entry page for the created session
        return redirect()->route('game.session.scores.enter', $gameSession->id);
    }

    // Show the score entry form for the session
    public function enterScores(GameSession $gameSession)
    {
        $gameSession->load('players', 'game');

        // Debugging
        //  dd($gameSession->game);
        

        return Inertia::render('Games/EnterScores', [
            'gameSession' => $gameSession,
            'players' => $gameSession->players,
            'game' => $gameSession->game,
        ]);
    }

    // Save scores for a session and mark session completed
    public function saveScores(Request $request, GameSession $gameSession)
    {
        $validated = $request->validate([
    'scores' => 'required|array',
    'scores.*.family_member_id' => 'required|exists:family_members,id',
    'scores.*.score' => 'required|integer|min:0',
]);


        foreach ($validated['scores'] as $entry) {
            GameScore::create([
                'game_session_id' => $gameSession->id,
                'family_member_id' => $entry['family_member_id'],
                'score' => $entry['score'],
            ]);
        }

        $gameSession->update(['status' => 'completed']);

        return redirect()->route('dashboard')->with('message', 'Scores saved! Session completed.');
    }
}
