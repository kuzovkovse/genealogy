<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\HistoricalFact;

class SendDailyFact extends Command
{
    protected $signature = 'telegram:daily-fact';
    protected $description = 'Send daily historical fact to Telegram users';

    public function handle()
    {
        $fact = HistoricalFact::where('is_active', true)
            ->orderByRaw('COALESCE(last_shown_at, "1970-01-01") ASC')
            ->inRandomOrder()
            ->first();

        if (!$fact) {
            $this->info('No active historical facts.');
            return;
        }

        $users = User::whereNotNull('telegram_chat_id')->get();

        foreach ($users as $user) {
            $this->sendMessage($user->telegram_chat_id,
                "🏛 *Исторический факт дня*\n\n" .
                $fact->content
            );
        }

        $fact->update([
            'last_shown_at' => now(),
        ]);

        $this->info('Daily fact sent.');
    }

    private function sendMessage($chatId, $text)
    {
        $token = config('services.telegram.bot_token');

        file_get_contents(
            "https://api.telegram.org/bot{$token}/sendMessage?" .
            http_build_query([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ])
        );
    }
}
