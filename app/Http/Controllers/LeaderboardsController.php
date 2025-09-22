<?php

namespace App\Http\Controllers;

use App\Models\GameScore;
use App\Models\GameSession;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LeaderboardsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $familyId = $user->family_id;

        // Preload relevant members to prevent N+1 queries
        $members = $this->getFamilyMembers($familyId);

        // Leaderboards
        $mostPlays     = $this->getTopMemberStats(GameScore::class, $familyId, 'family_member_id', 'plays', $members);
        $mostWins      = $this->getTopMemberStats(GameSession::class, $familyId, 'winner_family_member_id', 'wins', $members, true);
        $personalBests = $this->getTopMemberStats(GameScore::class, $familyId, 'family_member_id', 'best_score', $members, false, true);

        // Game Records
        $gameRecords = $this->getGameRecords($familyId);

        return Inertia::render('Leaderboards/Index', [
            'mostPlays'     => $mostPlays,
            'mostWins'      => $mostWins,
            'personalBests' => $personalBests,
            'gameRecords'   => $gameRecords,
        ]);
    }

    /**
     * Fetch all relevant family members as keyed collection
     */
    private function getFamilyMembers(int $familyId)
    {
        $memberIds = GameScore::where('family_id', $familyId)
            ->pluck('family_member_id')
            ->unique()
            ->toArray();

        $winnerIds = GameSession::where('family_id', $familyId)
            ->whereNotNull('winner_family_member_id')
            ->pluck('winner_family_member_id')
            ->toArray();

        $allMemberIds = array_unique(array_merge($memberIds, $winnerIds));

        return FamilyMember::whereIn('id', $allMemberIds)
            ->get(['id', 'name', 'avatar'])
            ->keyBy('id');
    }

    /**
     * Generic helper to get top member stats
     */
    private function getTopMemberStats(
        string $modelClass,
        int $familyId,
        string $memberColumn,
        string $statColumn,
        $members,
        bool $onlyWins = false,
        bool $useMax = false
    ) {
        $query = $modelClass::query()
            ->where('family_id', $familyId);

        if ($onlyWins) {
            $query->whereNotNull($memberColumn);
        }

        $selectExpr = $useMax
            ? DB::raw("MAX(score) as $statColumn")
            : DB::raw("COUNT(*) as $statColumn");

        $rows = $query->select($memberColumn, $selectExpr)
            ->groupBy($memberColumn)
            ->orderByDesc($statColumn)
            ->limit(10)
            ->get()
            ->map(fn($row) => [
                'member' => $members[$row->$memberColumn] ?? null,
                $statColumn => $row->$statColumn
            ]);

        return $rows;
    }

    /**
     * Game records: best score per game for family
     */
    private function getGameRecords(int $familyId)
    {
        return GameScore::join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
            ->join('games', 'game_sessions.game_id', '=', 'games.id')
            ->where('game_scores.family_id', $familyId)
            ->select(
                'game_sessions.game_id',
                'games.title',
                DB::raw('MAX(game_scores.score) as record')
            )
            ->groupBy('game_sessions.game_id', 'games.title')
            ->orderBy('games.title')
            ->get();
    }
}
