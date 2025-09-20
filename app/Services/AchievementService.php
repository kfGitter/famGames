<?php

namespace App\Services;

use App\Models\{Game, GameScore, GameSession, FamilyMember};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AchievementService
{
    /**
     * Entry point: Evaluate and award achievements after a game session.
     */
    public function evaluateAfterSession(GameSession $session): void
    {
        $this->checkMemberAchievements($session);
        $this->checkFamilyAchievements($session);
    }

    // ------------------------------------------------
    // 👤 MEMBER ACHIEVEMENTS
    // ------------------------------------------------
    private function checkMemberAchievements(GameSession $session): void
    {
        $this->checkActivePlayer($session);
        $this->checkStreaks($session);
        $this->checkOverallChamp($session);
        $this->checkVersatility($session);
        $this->checkNewbieSlayer($session);
        $this->checkComebackKid($session);
        $this->checkEarlyBird($session);
        $this->checkNightOwl($session);
        $this->checkPerGameWins($session);
        $this->checkTotalWins($session);
        $this->checkActiveChamp($session);
    }

    private function checkActivePlayer(GameSession $session): void
    {
        foreach ($this->memberIds($session) as $mid) {
            $plays = GameScore::where('family_id', $session->family_id)
                ->where('family_member_id', $mid)
                ->count();

            if ($plays >= 10) {
                FamilyMember::find($mid)?->awardAchievement('active_player');
            }
        }
    }

    private function checkStreaks(GameSession $session): void
    {
        if (!$session->winner_family_member_id) return;

        $streakService = app(\App\Services\StreakService::class);
        $winnerId = $session->winner_family_member_id;
        $familyId = $session->family_id;

        // 3 consecutive wins
        $last3 = GameSession::where('family_id', $familyId)
            ->whereNotNull('winner_family_member_id')
            ->orderByDesc('id')
            ->limit(3)
            ->pluck('winner_family_member_id');

        if ($last3->count() === 3 && $last3->every(fn($id) => $id === $winnerId)) {
            FamilyMember::find($winnerId)?->awardAchievement('triple_winner');
        }

        // Daily & weekly streaks
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

    private function checkOverallChamp(GameSession $session): void
    {
        foreach ($this->memberIds($session) as $mid) {
            $total = GameScore::where('family_id', $session->family_id)
                ->where('family_member_id', $mid)
                ->sum('score');

            if ($total >= 500) {
                FamilyMember::find($mid)?->awardAchievement('overall_champ');
            }
        }
    }

    private function checkVersatility(GameSession $session): void
    {
        $totalGames = Game::count();
        if ($totalGames === 0) return;

        foreach ($this->memberIds($session) as $mid) {
            $distinctGames = GameScore::join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
                ->where('game_scores.family_id', $session->family_id)
                ->where('game_scores.family_member_id', $mid)
                ->distinct('game_sessions.game_id')
                ->count('game_sessions.game_id');

            if ($distinctGames === $totalGames) {
                FamilyMember::find($mid)?->awardAchievement('versatility_badge');
            }
        }
    }

    private function checkNewbieSlayer(GameSession $session): void
    {
        if (!$session->winner_family_member_id) return;

        $newbies = GameScore::where('game_session_id', $session->id)
            ->pluck('family_member_id')
            ->filter(fn($mid) => GameScore::where('family_id', $session->family_id)
                ->where('family_member_id', $mid)
                ->count() === 1);

        if ($newbies->isNotEmpty()) {
            FamilyMember::find($session->winner_family_member_id)?->awardAchievement('newbie_slayer');
        }
    }

    private function checkComebackKid(GameSession $session): void
    {
        if (!$session->winner_family_member_id) return;

        $previous = GameSession::where('family_id', $session->family_id)
            ->where('id', '<', $session->id)
            ->orderByDesc('id')
            ->first();

        if ($previous && $previous->winner_family_member_id !== $session->winner_family_member_id) {
            FamilyMember::find($session->winner_family_member_id)?->awardAchievement('comeback_kid');
        }
    }

    private function checkEarlyBird(GameSession $session): void
    {
        foreach ($this->memberIds($session) as $mid) {
            $before10 = GameSession::join('game_scores', 'game_sessions.id', '=', 'game_scores.game_session_id')
                ->where('game_scores.family_member_id', $mid)
                ->whereTime('game_sessions.created_at', '<', '10:00:00')
                ->count();

            if ($before10 >= 3) {
                FamilyMember::find($mid)?->awardAchievement('early_bird');
            }
        }
    }

    private function checkNightOwl(GameSession $session): void
    {
        foreach ($this->memberIds($session) as $mid) {
            $after10pm = GameSession::join('game_scores', 'game_sessions.id', '=', 'game_scores.game_session_id')
                ->where('game_scores.family_member_id', $mid)
                ->where('game_scores.family_id', $session->family_id)
                ->whereTime('game_sessions.created_at', '>=', '22:00:00')
                ->count();

            if ($after10pm >= 3) {
                FamilyMember::find($mid)?->awardAchievement('night_owl');
            }
        }
    }

    private function checkPerGameWins(GameSession $session): void
    {
        if (!$session->winner_family_member_id) return;

        $winnerId = $session->winner_family_member_id;

        $winsPerGame = GameSession::where('family_id', $session->family_id)
            ->where('winner_family_member_id', $winnerId)
            ->where('game_id', $session->game_id)
            ->count();

        if ($winsPerGame >= 5) FamilyMember::find($winnerId)?->awardAchievement('game_boss_5');
        if ($winsPerGame >= 10) FamilyMember::find($winnerId)?->awardAchievement('game_master_10');
        if ($winsPerGame >= 20) FamilyMember::find($winnerId)?->awardAchievement('game_expert_20');
    }

    private function checkTotalWins(GameSession $session): void
    {
        if (!$session->winner_family_member_id) return;

        $winnerId = $session->winner_family_member_id;

        $totalWins = GameSession::where('family_id', $session->family_id)
            ->where('winner_family_member_id', $winnerId)
            ->count();

        if ($totalWins >= 5) FamilyMember::find($winnerId)?->awardAchievement('winner_boss_5');
        if ($totalWins >= 10) FamilyMember::find($winnerId)?->awardAchievement('winner_master_10');
        if ($totalWins >= 20) FamilyMember::find($winnerId)?->awardAchievement('winner_expert_20');
    }

    private function checkActiveChamp(GameSession $session): void
    {
        $last3Sessions = GameSession::where('family_id', $session->family_id)
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
    }

    // ------------------------------------------------
    // 👨‍👩‍👧‍👦 FAMILY ACHIEVEMENTS
    // ------------------------------------------------
    private function checkFamilyAchievements(GameSession $session): void
    {
        $this->checkFamilyChamp($session);
        $this->checkMilestones($session);
        $this->checkFamilyStreakBoss($session);
        $this->checkPartyExperts($session);
        $this->checkMarathonFamily($session);
        $this->checkFamilyWinMilestones($session);
        $this->checkFamilyPlayMilestones($session);
        $this->checkFamilyFavorite($session);
        $this->checkAllTogether($session);

    }

    private function checkFamilyChamp(GameSession $session): void
    {
        $totalFamilyPoints = GameScore::where('family_id', $session->family_id)->sum('score');
        if ($totalFamilyPoints >= 1000) {
            $session->family->awardAchievementToFamily('family_champ');
        }
    }

    private function checkMilestones(GameSession $session): void
    {
        $totalFamilyGames = GameSession::where('family_id', $session->family_id)->count();
        if ($totalFamilyGames >= 10) {
            $session->family->awardAchievementToFamily('family_milestone_10');
        }
    }

private function checkFamilyFavorite(GameSession $session): void
{
    $family = $session->family;

    $favoriteGame = GameSession::where('family_id', $family->id)
        ->select('game_id', DB::raw('COUNT(*) as total'))
        ->groupBy('game_id')
        ->orderByDesc('total')
        ->first();

    if ($favoriteGame && $favoriteGame->total >= 5) {
        $family->awardAchievementToFamily('family_favorite');
    }
}

private function checkAllTogether(GameSession $session): void
{
    $family = $session->family;
    $familyMemberCount = $family->members()->count();

    $sessionWithAll = GameScore::where('family_id', $family->id)
        ->select('game_session_id', DB::raw('COUNT(DISTINCT family_member_id) as participants'))
        ->groupBy('game_session_id')
        ->having('participants', '=', $familyMemberCount)
        ->exists();

    if ($sessionWithAll) {
        $family->awardAchievementToFamily('all_together');
    }
}

    private function checkFamilyStreakBoss(GameSession $session): void
    {
        $weekStart = Carbon::today()->startOfWeek();
        $playedDays = GameSession::where('family_id', $session->family_id)
            ->whereBetween('created_at', [$weekStart, $weekStart->copy()->endOfWeek()])
            ->selectRaw('DATE(created_at) as d')
            ->distinct()
            ->pluck('d')
            ->count();

        if ($playedDays >= 7) {
            $session->family->awardAchievementToFamily('family_streak_boss');
        }
    }

    private function checkPartyExperts(GameSession $session): void
    {
        $eveningSessions = GameSession::where('family_id', $session->family_id)
            ->whereDate('created_at', $session->created_at->toDateString())
            ->whereBetween('created_at', [
                $session->created_at->copy()->setTime(18, 0),
                $session->created_at->copy()->setTime(23, 59)
            ])
            ->count();

        if ($eveningSessions > 5) {
            $session->family->awardAchievementToFamily('party_animals');
        }
    }

    private function checkMarathonFamily(GameSession $session): void
    {
        $weekendSessions = GameSession::where('family_id', $session->family_id)
            ->whereBetween('created_at', [
                $session->created_at->copy()->startOfWeek()->addDays(5), // Friday
                $session->created_at->copy()->startOfWeek()->addDays(7)->endOfDay() // Sunday
            ])
            ->count();

        if ($weekendSessions >= 10) {
            $session->family->awardAchievementToFamily('marathon_family');
        }
    }

    private function checkFamilyWinMilestones(GameSession $session): void
    {
        $family = $session->family;
        $fid = $session->family_id;

        if ($family->members->every(fn($m) =>
            GameSession::where('family_id', $fid)->where('winner_family_member_id', $m->id)->exists()
        )) {
            $family->awardAchievementToFamily('family_of_winners');
        }

        if ($family->members->every(fn($m) =>
            GameSession::where('family_id', $fid)->where('winner_family_member_id', $m->id)->count() >= 5
        )) {
            $family->awardAchievementToFamily('family_of_experts');
        }

        if ($family->members->every(fn($m) =>
            GameSession::where('family_id', $fid)->where('winner_family_member_id', $m->id)->count() >= 10
        )) {
            $family->awardAchievementToFamily('are_you_humans');
        }
    }

    private function checkFamilyPlayMilestones(GameSession $session): void
    {
        $family = $session->family;
        $fid = $session->family_id;

        if ($family->members->every(fn($m) =>
            GameScore::where('family_id', $fid)->where('family_member_id', $m->id)->count() >= 3
        )) {
            $family->awardAchievementToFamily('active_family');
        }

        if ($family->members->every(fn($m) =>
            GameScore::where('family_id', $fid)->where('family_member_id', $m->id)->count() >= 10
        )) {
            $family->awardAchievementToFamily('super_active_family');
        }
    }

    // ------------------------------------------------
    // 🔧 Utility
    // ------------------------------------------------
    private function memberIds(GameSession $session)
    {
        return GameScore::where('game_session_id', $session->id)->pluck('family_member_id');
    }
}
