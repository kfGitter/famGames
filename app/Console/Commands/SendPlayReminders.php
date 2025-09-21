<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\PlayReminder;

class SendPlayReminders extends Command
{
    protected $signature = 'reminders:send {frequency=daily}';
    protected $description = 'Send daily or weekly play reminder emails';

    public function handle(): void
    {
        $frequency = $this->argument('frequency'); // daily or weekly

        // Example: notify only family account owners
        User::where('is_family_owner', true)->each(function ($user) use ($frequency) {
            $user->notify(new PlayReminder($frequency));
        });

        $this->info("{$frequency} reminders sent!");
    }
}
