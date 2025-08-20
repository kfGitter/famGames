<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\GameScore;
use App\Models\GameSession;
use App\Models\Game;

class FamilyMemberController extends Controller
{
    // Ensure user has a family and return members
    public function index(Request $request)
    {
        $user = $request->user();

        // If user has no family yet, create one and associate it
        if (! $user->family) {
            $family = Family::create(['name' => $user->name . "'s family"]);
            $user->family()->associate($family);
            $user->save();
        }

        $members = $user->family->members()->orderBy('name')->get();

        return Inertia::render('Family/Index', [
            'members' => $members,
            'auth' => ['user' => $user],
        ]);
    }

    // Show the 'create new member' form
    public function create(Request $request)
    {
        $user = $request->user();

        if (! $user->family) {
            $family = Family::create(['name' => $user->name . "'s family"]);
            $user->family()->associate($family);
            $user->save();
        }

        return Inertia::render('Family/Create');
    }

    // Store new member
    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->family) {
            $family = Family::create(['name' => $user->name . "'s family"]);
            $user->family()->associate($family);
            $user->save();
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:0',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public'); // storage/app/public/avatars
        }

        $member = $user->family->members()->create([
            'name' => $data['name'],
            'age' => $data['age'] ?? null,
            'avatar' => $path,
        ]);

        return redirect()->route('family-members.index')->with('success', 'Member added');
    }

    // Show single member profile
    public function show(Request $request, FamilyMember $member)
    {
        $user = $request->user();

        // Ensure the member belongs to this user's family
        if (! $user->family || $member->family_id !== $user->family->id) {
            abort(403);
        }

        $familyId = $user->family_id;

        // Total Games Played (count of score rows = sessions participated)
        $totalPlayed = GameScore::where('family_id', $familyId)
            ->where('family_member_id', $member->id)
            ->count();

        // Games Won
        $wins = GameSession::where('family_id', $familyId)
            ->where('winner_family_member_id', $member->id)
            ->count();

        // Top Scores by Game (max per game)
        $topScores = GameScore::query()
            ->join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
            ->join('games', 'game_sessions.game_id', '=', 'games.id')
            ->where('game_scores.family_id', $familyId)
            ->where('game_scores.family_member_id', $member->id)
            ->groupBy('game_sessions.game_id', 'games.title')
            ->select('game_sessions.game_id', 'games.title as game_title', DB::raw('MAX(game_scores.score) as best_score'))
            ->orderByDesc('best_score')
            ->limit(5)
            ->get();

        // Records they hold (within family):
        // Find per-game record then filter those where holder == member
        $records = GameScore::query()
            ->join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
            ->where('game_scores.family_id', $familyId)
            ->select('game_sessions.game_id', DB::raw('MAX(game_scores.score) as record'))
            ->groupBy('game_sessions.game_id');

        $memberRecords = GameScore::query()
            ->join('game_sessions', 'game_scores.game_session_id', '=', 'game_sessions.id')
            ->join('games', 'game_sessions.game_id', '=', 'games.id')
            ->joinSub($records, 'r', function ($join) {
                $join->on('r.game_id', '=', 'game_sessions.game_id')
                    ->on('r.record', '=', 'game_scores.score');
            })
            ->where('game_scores.family_id', $familyId)
            ->where('game_scores.family_member_id', $member->id)
            ->select('games.title as game_title', 'game_scores.score as record_score')
            ->orderBy('games.title')
            ->get();

        // Achievements
        $achievements = $member->achievements()
            ->get(['achievements.id', 'achievements.code', 'achievements.name', 'achievements.icon', 'achievements.description'])
            ->map(fn($a) => [
                'id' => $a->id,
                'code' => $a->code,
                'name' => $a->name,
                'icon' => $a->icon,
                'description' => $a->description,
                'awarded_at' => $a->pivot->awarded_at,
            ]);


        return Inertia::render('Family/Show', [
            'member'        => $member,
            'stats'         => [
                'totalPlayed' => $totalPlayed,
                'wins'        => $wins,
            ],
            'topScores'     => $topScores,
            'recordsHeld'   => $memberRecords,
            'achievements'  => $achievements,
        ]);
    }

    // Optional: delete a member
    public function destroy(Request $request, FamilyMember $member)
    {
        $user = $request->user();

        if (! $user->family || $member->family_id !== $user->family->id) {
            abort(403);
        }

        $member->delete();

        return redirect()->route('family-members.index')->with('success', 'Member removed');
    }
}
