<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameSession; // import this model
use App\Models\GameScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\CustomUserGame;


class GameSessionController extends Controller
{
    // Show page to start a new session for a game (select players)

public function create($id, $type = 'system')
{
    $user = Auth::user();

    if (! $user->family || $user->family->members->isEmpty()) {
        return redirect()->route('family-members.index')
            ->with('error', 'Add members in your family to start playing!');
    }

    $members = $user->family->members()->orderBy('name')->get();

    if ($type === 'custom') {
        $game = CustomUserGame::where('user_id', $user->id)->findOrFail($id);
    } else {
        $game = Game::findOrFail($id);
    }

    return Inertia::render('Games/Start', [
        'game' => $game,
        'members' => $members,
        'type' => $type,
    ]);
}

public function store(Request $request, $id, $type = 'system')
{
    $messages = [
        'players.min' => 'You must select at least 2 players to start a game.',
    ];

    $validated = $request->validate([
        'players' => 'required|array|min:2',
        'players.*' => 'exists:family_members,id',
    ], $messages);

    $user = Auth::user();

    if ($type === 'custom') {
        $game = CustomUserGame::where('user_id', $user->id)->findOrFail($id);
    } else {
        $game = Game::findOrFail($id);
    }

    $gameSession = GameSession::create([
        'user_id' => $user->id,
        'family_id' => $user->family_id,
        'game_id' => $game->id,
        'status' => 'in_progress',
        'is_custom' => $type === 'custom', // optional flag in DB
    ]);

    $gameSession->players()->attach($validated['players']);

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


        app(\App\Services\AchievementService::class)->evaluateAfterSession($gameSession);

        // (Optional) trigger achievements check here
        // app(AchievementService::class)->evaluateForSession($gameSession);

        return redirect()->route('dashboard')->with('message', 'Scores saved! Session completed.');
    }
}
