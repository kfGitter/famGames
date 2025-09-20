<?php

namespace App\Console\Commands;

use App\Models\FamilyMember;
use App\Models\Family;
use Illuminate\Console\Command;
use App\Services\StreakService;

class UpdateStreaks extends Command
{
    protected $signature = 'streaks:update';
    protected $description = 'Update streaks for families and members and award achievements';

    public function handle(StreakService $streakService)
    {
        $this->info('Updating streaks...');

        // Update all family members
    \App\Models\FamilyMember::all()->each(function ($member) use ($streakService) {
        $streakService->updateMemberStreaks($member, now());
    });

    // Update all families (only weekly available)
    
    Family::all()->each(function ($family) use ($streakService) {
    $streakService->updateFamilyDailyStreak($family, now());
    $streakService->updateFamilyWeeklyStreak($family, now()->startOfWeek());
});


    $this->info('Done.');
    return 0;
    }
}
