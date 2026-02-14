<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\DailyMemoryService;

class SendDailyFact extends Command
{
    protected $signature = 'telegram:daily-fact';
    protected $description = 'Send daily memory or historical fact to Telegram users';

    public function handle()
    {
        $message = app(DailyMemoryService::class)->getTodayMessage();

        if (!$message) {
            $this->info('No message to send.');
            return;
        }

        $users = User::whereNotNull('telegram_chat_id')->get();

        if ($users->isEmpty()) {
            $this->info('No users with Telegram connected.');
            return;
        }

        foreach ($users as $user) {
            $this->sendMessage(
                $user->telegram_chat_id,
                $message
            );
        }

        $this->info('Daily message sent.');
    }

    private function sendMessage($chatId, $text)
    {
        $token = config('services.telegram.bot_token');

        file_get_contents(
            "https://api.telegram.org/bot{$token}/sendMessage?" .
            http_build_query([
                'chat_id'   => $chatId,
                'text'      => $text,
                'parse_mode'=> 'Markdown',
            ])
        );
    }
}
