<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameSession;
use App\Models\GameScore;
use App\Models\CustomUserGame;
use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GameSessionController extends Controller
{
    /**
     * Show page to start a new session for a game.
     */
    public function create(int $id, string $type = 'system')
    {
        $user = Auth::user();

        if (! $user->family || $user->family->members->isEmpty()) {
            return redirect()->route('family-members.index')
                ->with('error', 'Add members in your family to start playing!');
        }

        $members = $user->family->members()->orderBy('name')->get();
        $game = $this->getGameByType($id, $type, $user);

        return Inertia::render('Games/Start', [
            'game' => $game,
            'members' => $members,
            'type' => $type,
        ]);
    }

    /**
     * Store a new game session.
     */
    public function store(Request $request, StreakService $streakService, int $id, string $type = 'system')
    {
        $validated = $request->validate([
            'players' => 'required|array|min:2',
            'players.*' => 'exists:family_members,id',
        ], [
            'players.min' => 'You must select at least 2 players to start a game.',
        ]);

        $user = Auth::user();
        $game = $this->getGameByType($id, $type, $user);

        // Create session
        $gameSession = GameSession::create([
            'user_id' => $user->id,
            'family_id' => $user->family_id,
            'game_id' => $game->id,
            'status' => 'in_progress',
            'is_custom' => $type === 'custom',
        ]);

        $gameSession->players()->attach($validated['players']);

        return redirect()->route('game.session.scores.enter', $gameSession->id);
    }

    /**
     * Show the score entry form for a session.
     */
    public function enterScores(GameSession $gameSession)
    {
        $gameSession->load('players', 'game');

        return Inertia::render('Games/EnterScores', [
            'gameSession' => $gameSession,
            'players' => $gameSession->players,
            'game' => $gameSession->game,
        ]);
    }

    /**
     * Save scores and update streaks.
     */
    public function saveScores(Request $request, GameSession $gameSession, StreakService $streakService)
    {
        $validated = $request->validate([
            'scores' => 'required|array|min:1',
            'scores.*.family_member_id' => 'required|exists:family_members,id',
            'scores.*.score' => 'required|integer|min:0',
        ]);

        $familyId = Auth::user()->family_id;

        foreach ($validated['scores'] as $entry) {
            GameScore::updateOrCreate(
                [
                    'game_session_id' => $gameSession->id,
                    'family_member_id' => $entry['family_member_id'],
                ],
                [
                    'family_id' => $familyId,
                    'score' => $entry['score'],
                ]
            );
        }

        // Determine winner (first max)
        $topScore = GameScore::where('game_session_id', $gameSession->id)
            ->orderByDesc('score')
            ->orderBy('id')
            ->first();

        $gameSession->update([
            'status' => 'completed',
            'winner_family_member_id' => $topScore?->family_member_id,
        ]);

        // Update streaks
        $date = now();
        foreach ($gameSession->players as $member) {
            $streakService->updateMemberStreaks($member, $date);
        }

        $streakService->updateFamilyWeeklyStreak($gameSession->family, $date->startOfWeek());
        $streakService->updateFamilyDailyStreak($gameSession->family, $date);

        // Evaluate achievements
        app(\App\Services\AchievementService::class)->evaluateAfterSession($gameSession);

        return redirect()->route('dashboard')
            ->with('message', 'Scores saved! Session completed.');
    }

    /**
     * Helper: fetch game by type
     */
    private function getGameByType(int $id, string $type, $user)
    {
        return $type === 'custom'
            ? CustomUserGame::where('user_id', $user->id)->findOrFail($id)
            : Game::findOrFail($id);
    }
}
