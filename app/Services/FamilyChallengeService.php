<?php

namespace App\Services;

use App\Models\Family;
use App\Models\FamilyChallenge;
use App\Models\GameSession;
use Carbon\Carbon;

class FamilyChallengeService
{
    /**
     * Recalculate progress for active challenges of a family after a session is saved.
     *
     * @param Family $family
     * @param GameSession $session
     * @return void
     */
    public function updateProgressAfterSession(Family $family, GameSession $session): void
    {
        // Reload active challenges for this family (not completed)
        $challenges = $family->challenges()->where('completed', false)->get();

        foreach ($challenges as $c) {
            switch ($c->type) {
                case 'daily':
                    // If session is today, mark progress 1 (goal typically 1)
                    $today = Carbon::today();
                    if ($session->created_at->isSameDay($today)) {
                        $c->progress = 1;
                    }
                    break;

                case 'weekly':
                    // Count sessions in current week for this family
                    $weekStart = Carbon::now()->startOfWeek();
                    $weekEnd = Carbon::now()->endOfWeek();
                    $count = GameSession::where('family_id', $family->id)
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->count();
                    $c->progress = min($c->goal, $count);
                    break;

                case 'hidden':
                    // Example: if the session used a game the family never played before (new game)
                    $playedGames = GameSession::where('family_id', $family->id)
                        ->distinct('game_id')
                        ->pluck('game_id')
                        ->toArray();

                    // If current session game_id is new and progress < goal, increment
                    if (!in_array($session->game_id, $playedGames)) {
                        $c->progress = min($c->goal, $c->progress + 1);
                    }
                    break;

                default:
                    // custom rules might go here
                    break;
            }

            // mark complete if reached goal
            if ($c->progress >= $c->goal) {
                $c->completed = true;
            }

            $c->save();
        }
    }
}
