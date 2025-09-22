<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

use App\Models\GameSession;
use App\Models\FamilyMember;
use App\Models\FamilyChallenge;
use App\Models\Challenge;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $family = $this->getFamily($user);
        $familyId = $family?->id;
        $windowDays = 7;

        $streakService = app(\App\Services\StreakService::class);
        $streaks = [
            'family_daily'  => $family ? $streakService->updateFamilyDailyStreak($family, Carbon::today()) : null,
            'family_weekly' => $family ? $streakService->updateFamilyWeeklyStreak($family, Carbon::today()->startOfWeek()) : null,
        ];

        $challenges = $this->loadChallenges($familyId);
        $history = $this->getActivityHistory($familyId);

        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => $user->only(['id', 'name', 'email', 'family_name', 'avatar']),
            ],
            'stats' => [
                'gamesPlayed'       => $this->gamesPlayed($familyId),
                'bestScore'         => $this->bestScore($familyId),
                'participationRate' => $this->participationRate($familyId, $windowDays),
                'mostActive'        => $this->mostActive($familyId, $windowDays),
                'window' => [
                    'days' => $windowDays,
                    'from' => now()->subDays($windowDays)->toDateString(),
                    'to'   => now()->toDateString(),
                ],
            ],
            'favoriteGames'      => $this->favoriteGames($user->id),
            'streaks'            => $streaks,
            'latestAchievements' => $this->latestAchievements($familyId),
            'challenges'         => $challenges,
            'activeChallenges'   => $challenges,
            'history'            => $history,
        ]);
    }

    // ------------------- PRIVATE HELPERS -------------------

    private function getFamily($user)
    {
        return $user->family;
    }

    private function loadChallenges(?int $familyId): Collection
    {
        if (!$familyId) return collect();

        $challenges = FamilyChallenge::with('challenge')
            ->where('family_id', $familyId)
            ->orderByRaw("
                CASE type
                    WHEN 'daily' THEN 1
                    WHEN 'weekly' THEN 2
                    WHEN 'hidden' THEN 3
                    ELSE 4
                END
            ")
            ->get();

        if ($challenges->where('completed', false)->isEmpty()) {
            foreach (Challenge::all() as $template) {
                FamilyChallenge::updateOrCreate(
                    ['family_id' => $familyId, 'challenge_id' => $template->id],
                    [
                        'title'       => $template->title,
                        'type'        => $template->type,
                        'description' => $template->description,
                        'goal'        => $template->goal ?? 1,
                        'progress'    => 0,
                        'completed'   => false,
                    ]
                );
            }

            $challenges = FamilyChallenge::with('challenge')
                ->where('family_id', $familyId)
                ->orderByRaw("
                    CASE type
                        WHEN 'daily' THEN 1
                        WHEN 'weekly' THEN 2
                        WHEN 'hidden' THEN 3
                        ELSE 4
                    END
                ")
                ->get();
        }

        return $challenges;
    }

    private function getActivityHistory(?int $familyId): array
{
    if (!$familyId) return [];

    $days = [];
    for ($i = 13; $i >= 0; $i--) {
        $day = now()->subDays($i)->startOfDay();
        $end = (clone $day)->endOfDay();

        $days[] = [
            'label' => $day->format('M d'), // e.g. "Sep 21"
            'value' => GameSession::where('family_id', $familyId)
                        ->whereBetween('created_at', [$day, $end])
                        ->count(),
        ];
    }

    return $days;
}


    private function latestAchievements(?int $familyId): array
    {
        if (!$familyId) return [];

        $memberAchievements = DB::table('family_member_achievements')
            ->join('achievements', 'family_member_achievements.achievement_id', '=', 'achievements.id')
            ->join('family_members', 'family_member_achievements.family_member_id', '=', 'family_members.id')
            ->where('family_members.family_id', $familyId)
            ->select(
                'achievements.id',
                'achievements.name',
                'achievements.icon',
                'achievements.description',
                'family_member_achievements.awarded_at',
                'family_members.name as member'
            )
            ->get()
            ->map(fn($row) => (array) $row + ['type' => 'member']);

        $familyAchievements = DB::table('family_achievements')
            ->join('achievements', 'family_achievements.achievement_id', '=', 'achievements.id')
            ->where('family_achievements.family_id', $familyId)
            ->select(
                'achievements.id',
                'achievements.name',
                'achievements.icon',
                'achievements.description',
                'family_achievements.awarded_at'
            )
            ->get()
            ->map(fn($row) => (array) $row + ['member' => null, 'type' => 'family']);

        return $memberAchievements
            ->merge($familyAchievements)
            ->sortByDesc(fn($r) => $r['awarded_at'] ?? null)
            ->take(3)
            ->values()
            ->all();
    }

    private function gamesPlayed(?int $familyId): int
    {
        return $familyId ? GameSession::where('family_id', $familyId)->count() : 0;
    }

    private function bestScore(?int $familyId): ?array
    {
        if (!$familyId) return null;

        $row = DB::table('game_scores')
            ->join('family_members', 'game_scores.family_member_id', '=', 'family_members.id')
            ->join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
            ->where('game_sessions.family_id', $familyId)
            ->select('family_members.name as member', DB::raw('MAX(game_scores.score) as score'))
            ->groupBy('family_members.name')
            ->orderByDesc('score')
            ->first();

        return $row ? ['member' => $row->member, 'score' => (int) $row->score] : null;
    }

    private function participationRate(?int $familyId, int $days): int
    {
        if (!$familyId) return 0;

        $totalMembers = FamilyMember::where('family_id', $familyId)->count();
        if ($totalMembers === 0) return 0;

        $since = now()->subDays($days);

        $participants = DB::table('game_session_family_member')
            ->join('game_sessions', 'game_session_family_member.game_session_id', '=', 'game_sessions.id')
            ->where('game_sessions.family_id', $familyId)
            ->where('game_sessions.created_at', '>=', $since)
            ->distinct('game_session_family_member.family_member_id')
            ->count('game_session_family_member.family_member_id');

        return (int) round(($participants / $totalMembers) * 100);
    }

    private function mostActive(?int $familyId, int $days): ?string
    {
        if (!$familyId) return null;

        $since = now()->subDays($days);

        $row = DB::table('game_session_family_member')
            ->join('game_sessions', 'game_session_family_member.game_session_id', '=', 'game_sessions.id')
            ->join('family_members', 'game_session_family_member.family_member_id', '=', 'family_members.id')
            ->where('game_sessions.family_id', $familyId)
            ->where('game_sessions.created_at', '>=', $since)
            ->select('family_members.name', DB::raw('COUNT(*) as plays'))
            ->groupBy('family_members.id', 'family_members.name')
            ->orderByDesc('plays')
            ->limit(1)
            ->first();

        return $row?->name;
    }

    private function favoriteGames(int $userId): array
    {
        $system = DB::table('games')
            ->join('game_user', 'games.id', '=', 'game_user.game_id')
            ->where('game_user.user_id', $userId)
            ->where('game_user.is_favorite', true)
            ->select(DB::raw("'system' as type"), 'games.id', 'games.title as name')
            ->orderBy('games.title')
            ->get();

        $custom = DB::table('custom_user_games')
            ->where('user_id', $userId)
            ->where('is_favorite', true)
            ->select(DB::raw("'custom' as type"), 'id', 'title as name')
            ->orderBy('title')
            ->get();

        return $system->merge($custom)->take(3)->values()->all();
    }

    public function manage()
    {
        $familyId = Auth::user()->family_id;

        return Inertia::render('Challenges/Manage', [
            'templates' => Challenge::select('id', 'title', 'type', 'description')->get(),
            'activeChallenges' => FamilyChallenge::with('challenge')
                ->where('family_id', $familyId)
                ->get(),
        ]);
    }
}
