<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\GenealogyFact;

class SendWeeklyGenealogyFact extends Command
{
    protected $signature = 'telegram:weekly-genealogy';
    protected $description = 'Send weekly genealogy fact to Telegram users';

    public function handle()
    {
        $fact = GenealogyFact::where('is_active', true)
            ->orderByRaw('COALESCE(last_shown_at, "1970-01-01") ASC')
            ->first();

        if (!$fact) {
            $this->info('No genealogy facts found.');
            return;
        }

        $users = User::whereNotNull('telegram_chat_id')->get();

        if ($users->isEmpty()) {
            $this->info('No users with Telegram connected.');
            return;
        }

        $message = "🧬 *Интересное о роде*\n\n"
            . $fact->content;

        foreach ($users as $user) {
            $this->sendMessage($user->telegram_chat_id, $message);
        }

        $fact->update([
            'last_shown_at' => now(),
        ]);

        $this->info('Weekly genealogy fact sent.');
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
