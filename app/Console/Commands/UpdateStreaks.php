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

        // Update all members and families using the service
        $streakService->updateAllStreaks();

        $this->info('Done.');
        return 0;
    }
}
