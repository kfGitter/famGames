<?php

namespace App\Http\Controllers;

use App\Models\GameScore;
use App\Models\GameSession;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LeaderboardsController extends Controller
{
    public function index()
    {
        $familyId = Auth::user()->family_id;

        // Most Plays (participation)
        $mostPlays = GameScore::query()
            ->select('family_member_id', DB::raw('COUNT(*) as plays'))
            ->where('family_id', $familyId)
            ->groupBy('family_member_id')
            ->orderByDesc('plays')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $row->member = FamilyMember::find($row->family_member_id, ['id','name','avatar']);
                return $row;
            });

        // Most Wins
        $mostWins = GameSession::query()
            ->select('winner_family_member_id as family_member_id', DB::raw('COUNT(*) as wins'))
            ->where('family_id', $familyId)
            ->whereNotNull('winner_family_member_id')
            ->groupBy('winner_family_member_id')
            ->orderByDesc('wins')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $row->member = FamilyMember::find($row->family_member_id, ['id','name','avatar']);
                return $row;
            });

        // Personal Best (top score per member across all games)
        // (We’ll find the single best score per member)
        $personalBests = GameScore::query()
            ->where('family_id', $familyId)
            ->select('family_member_id', DB::raw('MAX(score) as best_score'))
            ->groupBy('family_member_id')
            ->orderByDesc('best_score')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $row->member = FamilyMember::find($row->family_member_id, ['id','name','avatar']);
                return $row;
            });

        // Game Records (best score per game within this family)
        $gameRecords = GameScore::query()
            ->join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
            ->join('games', 'game_sessions.game_id', '=', 'games.id')
            ->where('game_scores.family_id', $familyId)
            ->select('game_sessions.game_id', 'games.title',
                DB::raw('MAX(game_scores.score) as record'))
            ->groupBy('game_sessions.game_id', 'games.title')
            ->orderBy('games.title')
            ->get();

        return Inertia::render('Leaderboards/Index', [
            'mostPlays'     => $mostPlays,
            'mostWins'      => $mostWins,
            'personalBests' => $personalBests,
            'gameRecords'   => $gameRecords,
        ]);
    }
}
