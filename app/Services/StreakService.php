<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Family;
use App\Models\FamilyMember;

class StreakService
{
    /**
     * Update an individual streak (strict logic: resets if not consecutive).
     */
    protected function updateIndividualStreak($model, string $cadence, Carbon $date)
    {
        $streakField     = $cadence . '_streak';
        $lastPlayedField = 'last_' . $cadence . '_played_at';

        $lastPlayed = $model->$lastPlayedField ? Carbon::parse($model->$lastPlayedField) : null;

        $isConsecutive = false;

        if ($cadence === 'daily') {
            $isConsecutive = $lastPlayed && $lastPlayed->diffInDays($date) === 1;
        }

        if ($cadence === 'weekly') {
            $isConsecutive = $lastPlayed && $lastPlayed->diffInWeeks($date) === 1;
        }

        if ($isConsecutive) {
            $model->$streakField++;
        } else {
            // start new streak (strict reset)
            $model->$streakField = 1;
        }

        $model->$lastPlayedField = $date;
        $model->save();

        return $model;
    }

    /**
     * Check if member played in the cadence period.
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
     * Update streaks for an individual family member (strict mode).
     */
    public function updateMemberStreaks(FamilyMember $member, Carbon $date): array
    {
        $streaks = [];

        foreach (['daily', 'weekly'] as $cadence) {
            if ($this->hasMemberPlayed($member, $cadence, $date)) {
                $this->updateIndividualStreak($member, $cadence, $date);
            } else {
                // missed → reset
                $streakField = $cadence . '_streak';
                $member->$streakField = 0;
                $member->save();
            }

            $streaks[$cadence] = [
                'current'     => $member->{$cadence . '_streak'},
                'last_played' => $member->{'last_' . $cadence . '_played_at'},
            ];
        }

        return $streaks;
    }

    /**
     * Update family's daily streak:
     * continues if at least ONE member played today.
     * More forgiving: if no one plays, it pauses (does not hard reset).
     */
    public function updateFamilyDailyStreak(Family $family, Carbon $today): array
    {
        $membersPlayedToday = FamilyMember::where('family_id', $family->id)
            ->whereDate('last_daily_played_at', $today->toDateString())
            ->count();

        if ($membersPlayedToday > 0) {
            // Increment only if not already counted today
            if (!$family->last_daily_played_at || !Carbon::parse($family->last_daily_played_at)->isSameDay($today)) {
                $family->daily_streak = ($family->daily_streak ?? 0) + 1;
                $family->last_daily_played_at = $today;
                $family->save();
            }
        }
        // else → no increment, no reset (family streak pauses)

        return [
            'current'     => $family->daily_streak ?? 0,
            'last_played' => $family->last_daily_played_at,
        ];
    }

    /**
     * Update family's weekly streak:
     * continues if at least HALF the members played together in one session this week.
     * More forgiving: no hard reset if missed.
     */
    public function updateFamilyWeeklyStreak(Family $family, Carbon $date): array
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek   = $date->copy()->endOfWeek();

        $allMemberIds = $family->members->pluck('id')->toArray();
        $totalMembers = count($allMemberIds);
        $required     = ceil($totalMembers / 2); // at least half

        $playedTogether = $family->sessions()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->whereHas('scores', function ($q) use ($allMemberIds, $required) {
                $q->whereIn('family_member_id', $allMemberIds);
            }, '>=', $required) // at least half must appear
            ->exists();

        if ($playedTogether) {
            if (!$family->last_weekly_played_at || Carbon::parse($family->last_weekly_played_at)->lt($startOfWeek)) {
                $family->weekly_streak = ($family->weekly_streak ?? 0) + 1;
                $family->last_weekly_played_at = $date;
                $family->save();
            }
        }
        // else → streak pauses

        return [
            'current'     => $family->weekly_streak ?? 0,
            'last_played' => $family->last_weekly_played_at,
        ];
    }
}
