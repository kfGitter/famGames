<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\GameSession;
use App\Models\FamilyMember;
use Carbon\Carbon;
use App\Models\FamilyChallenge;
use App\Models\Challenge;
use Illuminate\Support\Facades\Auth;
                use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $familyId = $user->family_id;
        $windowDays = 7;

        $familyDailyStreak = null;
        $familyWeeklyStreak = null;

        if ($family = $user->family) {
            $today = Carbon::today();
            $streakService = app(\App\Services\StreakService::class);
            $familyDailyStreak = $streakService->updateFamilyDailyStreak($family, $today);
            $familyWeeklyStreak = $streakService->updateFamilyWeeklyStreak($family, $today->startOfWeek());
        }

        // --- Challenges ---
        $challenges = [];
        $activeChallenges = [];

        if ($familyId) {
            // Load all challenges linked to family
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

            // Active challenges: not completed
            $activeChallenges = $challenges->where('completed', false);

            // If none exist, create from templates
            if ($activeChallenges->isEmpty()) {
                foreach (Challenge::all() as $template) {
                    FamilyChallenge::updateOrCreate(
                        [
                            'family_id'    => $familyId,
                            'challenge_id' => $template->id
                        ],
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

                // Reload challenges after creation
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
        }

        // --- History for activity chart ---
        $weeks = [];
        for ($i = 7; $i >= 0; $i--) {
            $start = Carbon::now()->startOfWeek()->subWeeks($i);
            $end = (clone $start)->endOfWeek();
            $label = $start->format('MMM d');
            $count = GameSession::where('family_id', $familyId)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $weeks[] = ['label' => $label, 'value' => $count];
        }

        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => $user->only(['id', 'name', 'email', 'family_name', 'avatar']),
            ],
            'stats' => [
                'gamesPlayed'      => $this->gamesPlayed($familyId),
                'bestScore'        => $this->bestScore($familyId),
                'participationRate'=> $this->participationRate($familyId, $windowDays),
                'mostActive'       => $this->mostActive($familyId, $windowDays),
                'window'           => [
                    'days' => $windowDays,
                    'from' => now()->subDays($windowDays)->toDateString(),
                    'to'   => now()->toDateString(),
                ],
            ],
            'favoriteGames'     => $this->favoriteGames($user->id),
            'streaks'           => [
                'family_daily'  => $familyDailyStreak,
                'family_weekly' => $familyWeeklyStreak,
            ],
            'latestAchievements'=> $this->latestAchievements($familyId),
            'challenges'        => $challenges,
            'activeChallenges'  => $challenges,

            'history'           => $weeks,
        ]);
    }


    private function latestAchievements(?int $familyId): array
    {
        if (!$familyId) {
            return [];
        }

        // Member achievements
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
            ->map(function ($row) {
                return (array) $row + ['type' => 'member'];
            });

        // Family achievements
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
            ->map(function ($row) {
                // family rows have no 'member' column
                $arr = (array) $row;
                $arr['member'] = null;
                $arr['type'] = 'family';
                return $arr;
            });

        // Merge, sort by awarded_at (desc), take top 3
        return $memberAchievements
            ->merge($familyAchievements)
            ->sortByDesc(function ($r) {
                return $r['awarded_at'] ?? null;
            })
            ->take(3)
            ->values()
            ->all();
    }


    // total games played
    private function gamesPlayed(?int $familyId): int
    {
        if (!$familyId) return 0;

        return GameSession::where('family_id', $familyId)->count();
    }


    // best score/scorer

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



    // participation rate (last 30 days)

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



    // most active member (last 30 days)
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

        return $row ? $row->name : null;
    }



    // favorite games
    private function favoriteGames(int $userId): array
    {
        // System favorites via pivot game_user.is_favorite
        $system = DB::table('games')
            ->join('game_user', 'games.id', '=', 'game_user.game_id')
            ->where('game_user.user_id', $userId)
            ->where('game_user.is_favorite', true)
            ->select(DB::raw("'system' as type"), 'games.id', 'games.title as name')
            ->orderBy('games.title')
            ->get();

        // Custom favorites (fallback: if column missing, comment the where on is_favorite)
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
