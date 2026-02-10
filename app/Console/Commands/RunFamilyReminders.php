<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReminderScheduler;

class RunFamilyReminders extends Command
{
    protected $signature = 'family:reminders';
    protected $description = 'Generate and send family reminders';

    public function handle(ReminderScheduler $scheduler): int
    {
        $this->info('⏳ Running family reminders…');

        $count = $scheduler->run();

        $this->info("✅ Created {$count} reminders");

        return self::SUCCESS;
    }
}
