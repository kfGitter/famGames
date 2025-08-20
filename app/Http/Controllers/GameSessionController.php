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
            'family_id' => Auth::user()->family_id,
            'game_id' => $game->id,
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
            'scores' => 'required|array|min:1',
            'scores.*.family_member_id' => 'required|exists:family_members,id',
            'scores.*.score' => 'required|integer|min:0',
        ]);

        $familyId = Auth::user()->family_id;

        // Save all scores
        foreach ($validated['scores'] as $entry) {
            GameScore::updateOrCreate(
                [
                    'game_session_id'   => $gameSession->id,
                    'family_member_id'  => $entry['family_member_id'],
                ],
                [
                    'family_id'         => $familyId,         // ✅ ensure not null
                    'score'             => $entry['score'],
                ]
            );
        }

        // Determine winner (single winner; tie-break = first max)
        $top = GameScore::where('game_session_id', $gameSession->id)
            ->orderByDesc('score')
            ->orderBy('id') // deterministic tie-breaker
            ->first();

        $gameSession->update([
            'status' => 'completed',
            'winner_family_member_id' => $top?->family_member_id, // nullable if somehow missing
        ]);

        // after $gameSession->update([...]);

        app(\App\Services\AchievementService::class)->evaluateAfterSession($gameSession);

        // (Optional) trigger achievements check here
        // app(AchievementService::class)->evaluateForSession($gameSession);

        return redirect()->route('dashboard')->with('message', 'Scores saved! Session completed.');
    }
}
