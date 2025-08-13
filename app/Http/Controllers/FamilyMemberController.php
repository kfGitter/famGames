<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

        return Inertia::render('Family/Show', [
            'member' => $member,
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
