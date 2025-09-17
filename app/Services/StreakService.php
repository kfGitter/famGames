<?php

namespace App\Services;

use App\Models\Streak;
use App\Models\Family;
use App\Models\FamilyMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StreakService
{
    /**
     * Update both daily and weekly streaks for a single member.
     */
    public function updateDailyAndWeeklyStreaks(FamilyMember $member, Carbon $date)
    {
        // Update daily streak
        $this->updateStreak($member, 'daily', $date->toDateString());

        // Update weekly streak (start of ISO week)
        $this->updateStreak($member, 'weekly', $date->startOfWeek()->toDateString());
    }

    /**
     * Update streaks for any streakable entity (member or family).
     * Polymorphic: $streakable can be FamilyMember or Family.
     */
    public function updateStreak($streakable, string $cadence, string $keyDate): Streak
    {
        $streak = Streak::firstOrCreate([
            'streakable_type' => get_class($streakable),
            'streakable_id'   => $streakable->id,
            'cadence'         => $cadence,
        ]);

        // Prevent double increment for same date/week
        if ($streak->last_date === $keyDate) {
            return $streak;
        }

        // Streak continuity check
        $expectedPrev = null;
        if ($streak->last_date) {
            $prev = Carbon::parse($streak->last_date);
            $expectedPrev = $cadence === 'daily'
                ? $prev->addDay()->toDateString()
                : $prev->addWeek()->startOfWeek()->toDateString();
        }

        // Increment if continuous, else reset
        if ($streak->last_date && $expectedPrev === $keyDate) {
            $streak->count++;
        } else {
            $streak->count = 1;
            $streak->started_at = $keyDate;
        }

        $streak->best = max($streak->best, $streak->count);
        $streak->last_date = $keyDate;
        $streak->save();

        return $streak;
    }

    public function updateFamilyWeeklyStreak(Family $family, Carbon $weekStart)
{
    // Check if all members played at least once this week
    $allPlayed = $family->members->every(function($member) use ($weekStart) {
        return $member->gameSessions()
            ->whereBetween('game_sessions.created_at', [$weekStart->copy()->startOfWeek(), $weekStart->copy()->endOfWeek()])
            ->exists();
    });

    if (! $allPlayed) {
        return null; // no streak update if any member missed
    }

    // Update family weekly streak
    $streak = Streak::firstOrCreate([
        'streakable_type' => Family::class,
        'streakable_id' => $family->id,
        'cadence' => 'weekly',
    ]);

    $keyDate = $weekStart->toDateString();

    if ($streak->last_date === $keyDate) {
        return $streak; // already updated this week
    }

    $expectedPrev = $streak->last_date
        ? Carbon::parse($streak->last_date)->addWeek()->startOfWeek()->toDateString()
        : null;

    if ($expectedPrev && $expectedPrev === $keyDate) {
        $streak->count++;
    } else {
        $streak->count = 1;
        $streak->started_at = $keyDate;
    }

    $streak->best = max($streak->best, $streak->count);
    $streak->last_date = $keyDate;
    $streak->save();

    return $streak;
}


    /**
     * Update all streaks for members and families.
     * Used by UpdateStreaks command.
     */#

     
    // public function updateAllStreaks()
    // {
    //     $date = now();

       // Update all family members
    //     foreach (FamilyMember::all() as $member) {
    //         $this->updateDailyAndWeeklyStreaks($member, $date);
    //     }

         // Update all families (weekly only)
    //     foreach (Family::all() as $family) {
    //         $this->updateStreak($family, 'weekly', $date->startOfWeek()->toDateString());
    //     }
    // }
}
