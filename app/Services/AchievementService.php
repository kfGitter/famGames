<?php

namespace App\Services;

use App\Models\{Game, GameScore, GameSession, FamilyMember};
use Illuminate\Support\Facades\DB;

class AchievementService
{
    // Call this AFTER scores are saved and winner is set
    public function evaluateAfterSession(GameSession $session): void
    {
        $familyId  = $session->family_id;
        $gameId    = $session->game_id;
        $memberIds = GameScore::where('game_session_id', $session->id)->pluck('family_member_id');

        // 1) Active Player ⚡ (>=10 sessions played)
        foreach ($memberIds as $mid) {
            $plays = GameScore::where('family_id', $familyId)->where('family_member_id', $mid)->count();
            if ($plays >= 10) FamilyMember::find($mid)?->awardAchievement('active_player');
        }

        // 2) Streak Master 🔥 (3 wins in a row)
        if ($session->winner_family_member_id) {
            $winnerId = $session->winner_family_member_id;
            $last3 = GameSession::where('family_id', $familyId)
                ->whereNotNull('winner_family_member_id')
                ->orderByDesc('id')
                ->limit(3)
                ->pluck('winner_family_member_id');
            if ($last3->count() === 3 && $last3->every(fn($id) => $id === $winnerId)) {
                FamilyMember::find($winnerId)?->awardAchievement('streak_master');
            }
        }
        // Alternative approach using StreakService (if you want to track daily streaks too)
        $dailyStreak = app(\App\Services\StreakService::class)->updateStreak($member, 'daily', \Carbon\Carbon::today()->toDateString());

        if ($dailyStreak->count >= 2) {
            FamilyMember::find($winnerId)?->awardAchievement('streak_master'); // fires your existing method
        }

        //2.5
        // Weekly family streak can award "Streak Boss 🔥"
        $weeklyFamilyStreak = app(\App\Services\StreakService::class)->updateFamilyWeeklyStreak($member->family, \Carbon\Carbon::today()->startOfWeek());
        if ($weeklyFamilyStreak && $weeklyFamilyStreak->count >= 1) {
            FamilyMember::find($winnerId)?->awardAchievement('streak_boss');
        }

        // 3) Overall Champ 👑 (>=500 cumulative points)
        foreach ($memberIds as $mid) {
            $total = GameScore::where('family_id', $familyId)->where('family_member_id', $mid)->sum('score');
            if ($total >= 500) FamilyMember::find($mid)?->awardAchievement('overall_champ');
        }

        // 4) Versatility 🎲 (played all games at least once)
        $totalGames = Game::count();
        if ($totalGames > 0) {
            foreach ($memberIds as $mid) {
                $distinctGames = GameScore::join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
                    ->where('game_scores.family_id', $familyId)
                    ->where('game_scores.family_member_id', $mid)
                    ->distinct('game_sessions.game_id')
                    ->count('game_sessions.game_id');
                if ($distinctGames === $totalGames) {
                    FamilyMember::find($mid)?->awardAchievement('versatility_badge');
                }
            }
        }

        // 5) Family Favorite 🎯 (most-played game in family; member has 5+ plays in it)
        $fav = GameSession::where('family_id', $familyId)
            ->select('game_id', DB::raw('COUNT(*) as c'))
            ->groupBy('game_id')->orderByDesc('c')->first();
        if ($fav) {
            foreach ($memberIds as $mid) {
                $playsInFav = GameScore::join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
                    ->where('game_scores.family_id', $familyId)
                    ->where('game_scores.family_member_id', $mid)
                    ->where('game_sessions.game_id', $fav->game_id)
                    ->count();
                if ($playsInFav >= 5) {
                    FamilyMember::find($mid)?->awardAchievement('family_favorite');
                }
            }
        }

        // 6) Newbie Slayer 😂 (you beat someone whose total plays becomes 1 after this session)
        if ($session->winner_family_member_id) {
            $newbies = GameScore::where('game_session_id', $session->id)
                ->pluck('family_member_id')
                ->filter(function ($mid) use ($familyId) {
                    $plays = GameScore::where('family_id', $familyId)->where('family_member_id', $mid)->count();
                    return $plays === 1; // i.e., this was their first session
                });
            if ($newbies->isNotEmpty()) {
                FamilyMember::find($session->winner_family_member_id)?->awardAchievement('newbie_slayer');
            }
        }
    }
}
