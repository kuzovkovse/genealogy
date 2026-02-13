<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Person;
use Carbon\Carbon;

class SendDailyBirthdays extends Command
{
    protected $signature = 'telegram:daily-birthdays';
    protected $description = 'Send daily birthday notifications to Telegram users';

    public function handle()
    {
        $today = Carbon::today();

        $people = Person::whereNotNull('birth_date')->get();

        $todayBirthdays = $people->filter(function ($person) use ($today) {
            $birth = Carbon::parse($person->birth_date);

            return $birth->day === $today->day &&
                $birth->month === $today->month;
        });

        if ($todayBirthdays->isEmpty()) {
            $this->info('No birthdays today.');
            return;
        }

        $users = User::whereNotNull('telegram_chat_id')->get();

        foreach ($users as $user) {

            $message = "🎉 Сегодня день рождения!\n\n";

            foreach ($todayBirthdays as $person) {

                $birth = Carbon::parse($person->birth_date);
                $age   = $today->year - $birth->year;

                $message .= "• {$person->first_name} {$person->last_name}\n";
                $message .= "  🎂 {$age} " . $this->plural($age) . "\n\n";
            }

            $this->sendMessage($user->telegram_chat_id, $message);
        }

        $this->info('Daily birthdays sent.');
    }

    private function plural($age)
    {
        if ($age % 10 == 1 && $age % 100 != 11) return 'год';
        if (in_array($age % 10, [2,3,4]) && !in_array($age % 100, [12,13,14])) return 'года';
        return 'лет';
    }

    private function sendMessage($chatId, $text)
    {
        $token = config('services.telegram.bot_token');

        file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?" . http_build_query([
                'chat_id' => $chatId,
                'text' => $text,
            ]));
    }
}
