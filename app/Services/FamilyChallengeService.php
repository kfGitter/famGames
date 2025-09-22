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
     */
    public function updateProgressAfterSession(Family $family, GameSession $session): void
    {
        $challenges = $family->challenges()->where('completed', false)->get();

        foreach ($challenges as $c) {
            switch ($c->type) {
                case 'daily':
                    $this->handleDaily($c, $session);
                    break;

                case 'weekly':
                    $this->handleWeekly($c, $session);
                    break;

                case 'hidden':
                    $this->handleHidden($c, $session);
                    break;

                // Extend here for streaks, narrative, creative, etc.
                default:
                    break;
            }

            // Mark complete if reached goal
            if ($c->progress >= $c->goal) {
                $c->completed = true;
            }

            $c->save();
        }
    }

    private function handleDaily(FamilyChallenge $c, GameSession $session): void
    {
        if ($session->created_at->isSameDay(Carbon::today())) {
            $c->progress = min($c->goal, $c->progress + 1);
        }
    }

    private function handleWeekly(FamilyChallenge $c, GameSession $session): void
    {
        $weekStart = $session->created_at->copy()->startOfWeek();
        $weekEnd   = $session->created_at->copy()->endOfWeek();

        $count = GameSession::where('family_id', $session->family_id)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        $c->progress = min($c->goal, $count);
    }

    private function handleHidden(FamilyChallenge $c, GameSession $session): void
    {
        $alreadyPlayed = GameSession::where('family_id', $session->family_id)
            ->where('id', '!=', $session->id)
            ->where('game_id', $session->game_id)
            ->exists();

        if (!$alreadyPlayed) {
            $c->progress = min($c->goal, $c->progress + 1);
        }
    }
}
