<?php 

namespace App\Services;

use Carbon\Carbon;
use App\Models\Family;
use App\Models\FamilyMember;

class StreakService
{
    /**
     * Core streak updater (low-level, protected).
     */
    protected function updateStreak($model, string $cadence, Carbon $date)
    {
        $streakField     = $cadence . '_streak';
        $lastPlayedField = 'last_' . $cadence . '_played_at';

        $lastPlayed = $model->$lastPlayedField ? Carbon::parse($model->$lastPlayedField) : null;
        $played     = false;

        if ($cadence === 'daily') {
            $played = $lastPlayed?->isSameDay($date) ?? false;
        }

        if ($cadence === 'weekly') {
            $played = $lastPlayed?->isSameWeek($date) ?? false;
        }

        if ($played) {
            return $model; // nothing to update
        }

        // increment streak
        $model->$streakField++;
        $model->$lastPlayedField = $date;

        $model->save();

        return $model;
    }

    /**
     * Check if a member has played in the cadence period.
     */
    protected function hasMemberPlayed(FamilyMember $member, string $cadence, Carbon $date): bool
    {
        if ($cadence === 'daily') {
            return $member->scores()->whereDate('created_at', $date)->exists();
        }

        if ($cadence === 'weekly') {
            return $member->scores()->whereBetween(
                'created_at',
                [$date->copy()->startOfWeek(), $date->copy()->endOfWeek()]
            )->exists();
        }

        return false;
    }

    /**
     * Update streaks for a single member.
     */
    public function updateMemberStreaks(FamilyMember $member, Carbon $date): array
    {
        $streaks = [];

        foreach (['daily', 'weekly'] as $cadence) {
            if ($this->hasMemberPlayed($member, $cadence, $date)) {
                $this->updateStreak($member, $cadence, $date);
            } else {
                // reset streak if member did not play
                $streakField = $cadence . '_streak';
                $member->$streakField = 0;
                $member->save();
            }

            $streaks[$cadence] = [
                'current' => $member->{$cadence . '_streak'},
                // removed 'best' since no column exists
            ];
        }

        return $streaks;
    }

    /**
     * Update family's daily streak: increments if at least one member played today.
     */
    public function updateFamilyDailyStreak(Family $family, Carbon $today): array
    {
        $membersPlayedToday = FamilyMember::where('family_id', $family->id)
            ->whereDate('last_daily_played_at', $today->toDateString())
            ->count();

        if ($membersPlayedToday > 0 && (!$family->last_daily_played_at || !Carbon::parse($family->last_daily_played_at)->isSameDay($today))) {
    $family->daily_streak = ($family->daily_streak ?? 0) + 1;
    $family->last_daily_played_at = $today;
    $family->save();
}

        return [
            'current' => $family->daily_streak ?? 0,
            'last_played' => $family->last_daily_played_at,
        ];
    }

    /**
     * Update family's weekly streak: increments only if all members played at least once during the week.
     */
    public function updateFamilyWeeklyStreak(Family $family, Carbon $startOfWeek): array
    {
        $allPlayed = $family->members->every(function ($member) use ($startOfWeek) {
            return $member->last_daily_played_at && Carbon::parse($member->last_daily_played_at)->gte($startOfWeek);
        });

        if ($allPlayed && (!$family->last_weekly_played_at || Carbon::parse($family->last_weekly_played_at)->lt($startOfWeek))) {
    $family->weekly_streak = ($family->weekly_streak ?? 0) + 1;
    $family->last_weekly_played_at = now();
    $family->save();
}


        return [
            'current' => $family->weekly_streak ?? 0,
            'last_played' => $family->last_weekly_played_at,
        ];
    }
}
