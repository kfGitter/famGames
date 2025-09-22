<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\GameScore;
use App\Models\GameSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\StreakService;

class FamilyMemberController extends Controller
{
    /**
     * List all family members for authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $this->ensureUserFamily($user);

        $members = $user->family->members()->orderBy('name')->get();

        return Inertia::render('Family/Index', [
            'members' => $members,
            'auth' => ['user' => $user],
        ]);
    }

    /**
     * Show the form to create a new family member.
     */
    public function create(Request $request)
    {
        $this->ensureUserFamily($request->user());
        return Inertia::render('Family/Create');
    }

    /**
     * Store a new family member.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $this->ensureUserFamily($user);

        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'age'    => 'nullable|integer|min:0',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $path = $request->hasFile('avatar') ? $request->file('avatar')->store('avatars', 'public') : null;

        $user->family->members()->create([
            'name'   => $data['name'],
            'age'    => $data['age'] ?? null,
            'avatar' => $path,
        ]);

        return redirect()->route('family-members.index')->with('success', 'Member added');
    }

    /**
     * Show a single member profile with stats.
     */
    public function show(Request $request, FamilyMember $member)
    {
        $user = $request->user();
        $this->authorizeMember($user, $member);

        $familyId = $user->family_id;

        $topScores     = $this->getTopScores($member, $familyId);
        $memberRecords = $this->getMemberRecords($member, $familyId);
        $achievements  = $this->getAchievements($member);

        $streakService = app(StreakService::class);
        $streaks       = $streakService->updateMemberStreaks($member, Carbon::today());

        return Inertia::render('Family/Show', [
            'member'      => $member,
            'stats'       => [
                'totalPlayed' => GameScore::where('family_id', $familyId)
                                           ->where('family_member_id', $member->id)
                                           ->count(),
                'wins'        => GameSession::where('family_id', $familyId)
                                           ->where('winner_family_member_id', $member->id)
                                           ->count(),
            ],
            'topScores'   => $topScores,
            'recordsHeld' => $memberRecords,
            'achievements'=> $achievements,
            'streaks'     => $streaks,
        ]);
    }

    /**
     * Delete a family member.
     */
    public function destroy(Request $request, FamilyMember $member)
    {
        $this->authorizeMember($request->user(), $member);
        $member->delete();

        return redirect()->route('family-members.index')->with('success', 'Member removed');
    }

    // -------------------- PRIVATE HELPERS --------------------

    /**
     * Ensure the user has a family; create one if not.
     */
    private function ensureUserFamily($user): void
    {
        if (! $user->family) {
            $family = Family::create(['name' => $user->name . "'s family"]);
            $user->family()->associate($family);
            $user->save();
        }
    }

    /**
     * Authorize that the member belongs to the user's family.
     */
    private function authorizeMember($user, FamilyMember $member): void
    {
        if (! $user->family || $member->family_id !== $user->family->id) {
            abort(403);
        }
    }

    /**
     * Get top scores per game for a member.
     */
    private function getTopScores(FamilyMember $member, int $familyId)
    {
        return GameScore::query()
            ->join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
            ->join('games', 'game_sessions.game_id', '=', 'games.id')
            ->where('game_scores.family_id', $familyId)
            ->where('game_scores.family_member_id', $member->id)
            ->groupBy('game_sessions.game_id', 'games.title')
            ->select('game_sessions.game_id', 'games.title as game_title', DB::raw('MAX(game_scores.score) as best_score'))
            ->orderByDesc('best_score')
            ->limit(5)
            ->get();
    }

    /**
     * Get per-member game records they hold.
     */
    private function getMemberRecords(FamilyMember $member, int $familyId)
    {
        $records = GameScore::query()
            ->join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
            ->where('game_scores.family_id', $familyId)
            ->select('game_sessions.game_id', DB::raw('MAX(game_scores.score) as record'))
            ->groupBy('game_sessions.game_id');

        return GameScore::query()
            ->join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
            ->join('games', 'game_sessions.game_id', '=', 'games.id')
            ->joinSub($records, 'r', function ($join) {
                $join->on('r.game_id', '=', 'game_sessions.game_id')
                     ->on('r.record', '=', 'game_scores.score');
            })
            ->where('game_scores.family_member_id', $member->id)
            ->where('game_scores.family_id', $familyId)
            ->select('games.title as game_title', 'game_scores.score as record_score')
            ->orderBy('games.title')
            ->get();
    }

    /**
     * Format achievements for frontend.
     */
    private function getAchievements(FamilyMember $member)
    {
        return $member->achievements()
            ->get(['achievements.id', 'achievements.code', 'achievements.name', 'achievements.icon', 'achievements.description'])
            ->map(fn($a) => [
                'id'          => $a->id,
                'code'        => $a->code,
                'name'        => $a->name,
                'icon'        => $a->icon,
                'description' => $a->description,
                'awarded_at'  => $a->pivot->awarded_at,
            ]);
    }
}
