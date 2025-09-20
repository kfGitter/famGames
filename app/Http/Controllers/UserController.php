<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'], // 2MB max
        ]);

        $user = Auth::user();

        // Store in public storage
        $path = $request->file('avatar')->store('avatars', 'public');

        // Save path in DB
        
        $user = Auth::user();
        $user = \App\Models\User::find($user->id); // ensures Eloquent model


        $user->avatar = $path;
        $user->save();

        return back()->with('success', 'Avatar updated!');
    }
}
