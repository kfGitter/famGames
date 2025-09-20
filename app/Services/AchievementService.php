<?php

namespace App\Services;

use App\Models\{Game, GameScore, GameSession, FamilyMember};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AchievementService
{
    /**
     * Evaluate and award achievements after a game session.
     */
    public function evaluateAfterSession(GameSession $session): void
    {
        $familyId  = $session->family_id;
        $family    = $session->family;
        $gameId    = $session->game_id;

        $memberIds = GameScore::where('game_session_id', $session->id)
            ->pluck('family_member_id');

        $streakService = app(\App\Services\StreakService::class);

        // ------------------------------------------------
        // 👤 MEMBER ACHIEVEMENTS
        // ------------------------------------------------

        // Active Player ⚡ (>=10 sessions played)
        foreach ($memberIds as $mid) {
            $plays = GameScore::where('family_id', $familyId)
                ->where('family_member_id', $mid)
                ->count();
            if ($plays >= 10) {
                FamilyMember::find($mid)?->awardAchievement('active_player');
            }
        }

        // Streak Master 🔥 (3 wins in a row OR via daily streak check)
        if ($session->winner_family_member_id) {
            $winnerId = $session->winner_family_member_id;

            // 3 consecutive wins
            $last3 = GameSession::where('family_id', $familyId)
                ->whereNotNull('winner_family_member_id')
                ->orderByDesc('id')
                ->limit(3)
                ->pluck('winner_family_member_id');

            if ($last3->count() === 3 && $last3->every(fn($id) => $id === $winnerId)) {
                FamilyMember::find($winnerId)?->awardAchievement('triple_winner');
            }

            // Daily & Weekly streak updates
            $member = FamilyMember::find($winnerId);
            if ($member) {
                $streaks = $streakService->updateMemberStreaks($member, Carbon::today());

                if ($streaks['daily']['current'] >= 3) {
                    $member->awardAchievement('streak_boss');
                }
                if ($streaks['daily']['current'] >= 7) {
                    $member->awardAchievement('daily_streak_master');
                }
                if ($streaks['weekly']['current'] >= 4) {
                    $member->awardAchievement('weekly_streak_master');
                }
            }
        }

        // Overall Champ 👑 (>=500 cumulative points)
        foreach ($memberIds as $mid) {
            $total = GameScore::where('family_id', $familyId)
                ->where('family_member_id', $mid)
                ->sum('score');
            if ($total >= 500) {
                FamilyMember::find($mid)?->awardAchievement('overall_champ');
            }
        }

        // Versatility Ninja 🎲 (played all games in the library)
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

        // Newbie Slayer 😂 (beat a member with only 1 session so far)
        if ($session->winner_family_member_id) {
            $newbies = GameScore::where('game_session_id', $session->id)
                ->pluck('family_member_id')
                ->filter(fn($mid) => GameScore::where('family_id', $familyId)
                    ->where('family_member_id', $mid)
                    ->count() === 1);

            if ($newbies->isNotEmpty()) {
                FamilyMember::find($session->winner_family_member_id)?->awardAchievement('newbie_slayer');
            }
        }

        // Comeback Kid 🌀 (winner was last before)
        if ($session->winner_family_member_id) {
            $winnerId = $session->winner_family_member_id;

            $previous = GameSession::where('family_id', $familyId)
                ->where('id', '<', $session->id)
                ->orderByDesc('id')
                ->first();

            if ($previous && $previous->winner_family_member_id !== $winnerId) {
                FamilyMember::find($winnerId)?->awardAchievement('comeback_kid');
            }
        }

        // Early Bird 🌞 (joined 3 sessions before 10am)

        foreach ($memberIds as $mid) {
            $joinedBefore10 = GameSession::join('game_scores', 'game_sessions.id', '=', 'game_scores.game_session_id')
                ->where('game_scores.family_member_id', $mid)
                ->where('game_sessions.id', $session->id)
                ->whereTime('game_sessions.created_at', '<', '10:00:00')
                ->count();

            if ($joinedBefore10 >= 3) {
                FamilyMember::find($mid)?->awardAchievement('early_bird');
            }
        }
        // ------------------------------------------------

        // Night Owl 🌙 (joined 3 sessions after 10pm)
        foreach ($memberIds as $mid) {
            $latePlays = GameSession::join('game_scores', 'game_sessions.id', '=', 'game_scores.game_session_id')
                ->where('game_scores.family_member_id', $mid)
                ->where('game_scores.family_id', $familyId)
                ->whereTime('game_sessions.created_at', '>=', '22:00:00')
                ->count();
            if ($latePlays >= 3) {
                FamilyMember::find($mid)?->awardAchievement('night_owl');
            }
        }

        // ------------------------------------------------
        // 👨‍👩‍👧‍👦 FAMILY ACHIEVEMENTS
        // ------------------------------------------------

        // Family Champ 👑 (>=1000 cumulative family points)
        $totalFamilyPoints = GameScore::where('family_id', $familyId)->sum('score');
        if ($totalFamilyPoints >= 1000) {
            $family->awardAchievementToFamily('family_champ');
        }

        // Milestone 10 🎉 (10 family sessions played)
        $totalFamilyGames = GameSession::where('family_id', $familyId)->count();
        if ($totalFamilyGames >= 10) {
            $family->awardAchievementToFamily('family_milestone_10');
        }

        // Family Streak Boss 🔥 (played every day in a week)
$weekStart = Carbon::today()->startOfWeek();
$playedDays = GameSession::where('family_id', $familyId)
    ->whereBetween('created_at', [$weekStart, $weekStart->copy()->endOfWeek()])
    ->selectRaw('DATE(created_at) as d')
    ->distinct()
    ->pluck('d')
    ->count();

if ($playedDays >= 7) {
    $family->awardAchievementToFamily('family_streak_boss');
}

        // Party Experts 🎊 (5+ games in one evening)
        $eveningSessions = GameSession::where('family_id', $familyId)
            ->whereDate('created_at', $session->created_at->toDateString())
            ->whereBetween('created_at', [
                $session->created_at->copy()->setTime(18, 0),
                $session->created_at->copy()->setTime(23, 59)
            ])
            ->count();
        if ($eveningSessions > 5) {
            $family->awardAchievementToFamily('party_animals');
        }

        // Marathon Family 🏃‍♂️ (10+ sessions in one weekend)
        $weekendSessions = GameSession::where('family_id', $familyId)
            ->whereBetween('created_at', [
                $session->created_at->copy()->startOfWeek()->addDays(5), // Friday
                $session->created_at->copy()->startOfWeek()->addDays(7)->endOfDay() // Sunday
            ])
            ->count();
        if ($weekendSessions >= 10) {
            $family->awardAchievementToFamily('marathon_family');
        }

        // ------------------------------------------------
// 🏆 New per-game win milestones
// ------------------------------------------------
if ($session->winner_family_member_id) {
    $winnerId = $session->winner_family_member_id;

    $winsPerGame = GameSession::where('family_id', $familyId)
        ->where('winner_family_member_id', $winnerId)
        ->where('game_id', $session->game_id)
        ->count();

    if ($winsPerGame >= 5) {
        FamilyMember::find($winnerId)?->awardAchievement('game_boss_5');
    }
    if ($winsPerGame >= 10) {
        FamilyMember::find($winnerId)?->awardAchievement('game_master_10');
    }
    if ($winsPerGame >= 20) {
        FamilyMember::find($winnerId)?->awardAchievement('game_expert_20');
    }
}

// ------------------------------------------------
// 🏆 Overall wins (any games)
// ------------------------------------------------
if ($session->winner_family_member_id) {
    $winnerId = $session->winner_family_member_id;

    $totalWins = GameSession::where('family_id', $familyId)
        ->where('winner_family_member_id', $winnerId)
        ->count();

    if ($totalWins >= 5) {
        FamilyMember::find($winnerId)?->awardAchievement('winner_boss_5');
    }
    if ($totalWins >= 10) {
        FamilyMember::find($winnerId)?->awardAchievement('winner_master_10');
    }
    if ($totalWins >= 20) {
        FamilyMember::find($winnerId)?->awardAchievement('winner_expert_20');
    }
}

// ------------------------------------------------
// 👤 Active Champ (most active in last 3 sessions)
// ------------------------------------------------
$last3Sessions = GameSession::where('family_id', $familyId)
    ->orderByDesc('id')
    ->limit(3)
    ->pluck('id');

$activity = GameScore::whereIn('game_session_id', $last3Sessions)
    ->select('family_member_id', DB::raw('COUNT(*) as plays'))
    ->groupBy('family_member_id')
    ->orderByDesc('plays')
    ->first();

if ($activity) {
    FamilyMember::find($activity->family_member_id)?->awardAchievement('active_champ');
}

// ------------------------------------------------
// 👨‍👩‍👧‍👦 Family win/play milestones
// ------------------------------------------------

// Family of Winners (all have at least 1 win)
if ($family->members->every(fn($m) =>
    GameSession::where('family_id', $familyId)->where('winner_family_member_id', $m->id)->exists()
)) {
    $family->awardAchievementToFamily('family_of_winners');
}

// Family of Experts (all have at least 5 wins)
if ($family->members->every(fn($m) =>
    GameSession::where('family_id', $familyId)->where('winner_family_member_id', $m->id)->count() >= 5
)) {
    $family->awardAchievementToFamily('family_of_experts');
}

// Are you even humans?? (all have at least 10 wins)
if ($family->members->every(fn($m) =>
    GameSession::where('family_id', $familyId)->where('winner_family_member_id', $m->id)->count() >= 10
)) {
    $family->awardAchievementToFamily('are_you_humans');
}

// Active Family (all played >=3 sessions)
if ($family->members->every(fn($m) =>
    GameScore::where('family_id', $familyId)->where('family_member_id', $m->id)->count() >= 3
)) {
    $family->awardAchievementToFamily('active_family');
}

// Super Active Family (all played >=10 sessions)
if ($family->members->every(fn($m) =>
    GameScore::where('family_id', $familyId)->where('family_member_id', $m->id)->count() >= 10
)) {
    $family->awardAchievementToFamily('super_active_family');
}
    }
}
