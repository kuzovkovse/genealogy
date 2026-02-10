<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Person;
use App\Models\User;
use App\Services\ReminderMessageService;

class TestBirthdayReminder extends Command
{
    protected $signature = 'reminder:test-birthday {personId} {userId}';

    protected $description = 'Test birthday reminder';

    public function handle(ReminderMessageService $service)
    {
        $person = Person::findOrFail($this->argument('personId'));
        $user   = User::findOrFail($this->argument('userId'));

        $message = $service->birthday($person, $user);

        dump($message);
    }
}
