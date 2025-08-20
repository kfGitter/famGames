<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AchievementController extends Controller
{
    // 1) Show all family members
    public function index()
    {
        $familyId = Auth::user()->family_id;

        $members = FamilyMember::where('family_id', $familyId)
            ->select('id', 'name', 'avatar')
            ->get();

        return Inertia::render('Achievements/Index', [
            'members' => $members,
        ]);
    }

    // 2) Show specific member achievements
    public function show(FamilyMember $member)
    {
        $user = Auth::user();
        if ($member->family_id !== $user->family_id) {
            abort(403);
        }

        $achievements = $member->achievements()
    ->withPivot('awarded_at')
    ->get(['achievements.id','achievements.code','achievements.name','achievements.icon','achievements.description'])
    ->map(fn($a) => [
        'id'          => $a->id,
        'code'        => $a->code,
        'name'        => $a->name,
        'icon'        => $a->icon,
        'description' => $a->description,
        'awarded_at'  => $a->pivot->awarded_at,
    ]);


        return Inertia::render('Achievements/Show', [
            'member' => $member,
            'achievements' => $achievements,
        ]);
    }
}
