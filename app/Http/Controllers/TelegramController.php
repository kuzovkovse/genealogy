<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        if (!isset($data['message'])) {
            return response()->json(['ok' => true]);
        }

        $chatId = $data['message']['chat']['id'];
        $text   = trim($data['message']['text'] ?? '');

        if ($text === '/start') {
            $this->sendMessage($chatId,
                "👋 Добро пожаловать в ПомниКорни!\n\nВведите код подключения из вашего профиля."
            );
            return response()->json(['ok' => true]);
        }

        if ($text === '/birthdays') {
            $this->sendBirthdays($chatId);
            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => true]);
    }

    private function sendBirthdays($chatId)
    {
        $today = now();
        $in7   = now()->addDays(7);

        $people = \App\Models\Person::whereNotNull('birth_date')->get();

        $upcoming = $people->filter(function ($person) use ($today, $in7) {
            $birthdayThisYear = \Carbon\Carbon::parse($person->birth_date)
                ->year($today->year);

            return $birthdayThisYear->between($today, $in7);
        });

        if ($upcoming->isEmpty()) {
            $this->sendMessage($chatId, "🎂 В ближайшие 7 дней дней рождения нет.");
            return;
        }

        $message = "🎂 Ближайшие дни рождения:\n\n";

        foreach ($upcoming as $person) {
            $birthDate = \Carbon\Carbon::parse($person->birth_date);
            $birthdayThisYear = $birthDate->year($today->year);

            $age = $today->year - $birthDate->year;

            $message .= "• {$person->first_name} {$person->last_name}\n";
            $message .= "  📅 " . $birthdayThisYear->format('d.m') . "\n";
            $message .= "  🎈 Исполняется {$age}\n\n";
        }

        $this->sendMessage($chatId, $message);
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
