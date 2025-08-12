<?php

namespace App\Http\Controllers;


use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class UserGameController extends Controller
{
    public function store(Game $game): RedirectResponse
    {
        Auth::user()->games()->syncWithoutDetaching($game->id);
        return back()->with('message', 'Game added to your library!');
    }
}
