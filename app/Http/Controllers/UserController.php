<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => ['required', 'image', 'max:2048'],
    ]);

    $user = Auth::user();

    $path = $request->file('avatar')->store('avatars', 'public');

    
    $user->avatar = $path;
    $user->save();

    return redirect()->back()->with('success', 'Avatar updated!');
}

}
