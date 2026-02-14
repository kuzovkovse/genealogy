<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SendDailyFact extends Command
{
    protected $signature = 'telegram:daily-fact';
    protected $description = 'Send daily historical fact to Telegram users';

    public function handle()
    {
        $facts = $this->facts();

        $fact = $facts[array_rand($facts)];

        $users = User::whereNotNull('telegram_chat_id')->get();

        if ($users->isEmpty()) {
            $this->info('No Telegram users connected.');
            return;
        }

        foreach ($users as $user) {
            $this->sendMessage($user->telegram_chat_id, $fact);
        }

        $this->info('Daily fact sent successfully.');
    }

    private function facts()
    {
        return [
            "🏛 *Исторический факт дня*\n\nРодовые книги дворян велись официально государством.\n\nБерегите историю своей семьи.",
            "📜 *Исторический факт дня*\n\nВ XIX веке в России существовали метрические книги — основной источник генеалогии.\n\nКаждая запись — это след судьбы.",
            "⚔ *Исторический факт дня*\n\nМногие крестьяне получали фамилии только в конце XIX века.\n\nФамилия — это память о предках.",
            "🏡 *Исторический факт дня*\n\nДо революции семьи часто жили несколькими поколениями под одной крышей.\n\nРод — это связь поколений.",
            "📖 *Исторический факт дня*\n\nПервая всеобщая перепись населения в России прошла в 1897 году.\n\nСегодня вы можете вести свою собственную историю."
        ];
    }

    private function sendMessage($chatId, $text)
    {
        $token = config('services.telegram.bot_token');

        file_get_contents(
            "https://api.telegram.org/bot{$token}/sendMessage?" .
            http_build_query([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ])
        );
    }
}
