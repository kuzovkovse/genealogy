<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Person;
use Carbon\Carbon;

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

        // Проверяем — уже подключён ли этот Telegram
        $user = User::where('telegram_chat_id', $chatId)->first();

        // /start всегда показывает инструкцию
        if ($text === '/start') {
            if ($user) {
                $this->sendMessage($chatId,
                    "✅ Ваш Telegram уже подключён.\n\nДоступные команды:\n/birthdays — ближайшие дни рождения"
                );
            } else {
                $this->sendMessage($chatId,
                    "👋 Добро пожаловать в ПомниКорни!\n\nВведите код подключения из вашего профиля."
                );
            }

            return response()->json(['ok' => true]);
        }

        // Если пользователь НЕ подключён — ждём код
        if (!$user) {

            $connectUser = User::where('telegram_connect_code', $text)->first();

            if (!$connectUser) {
                $this->sendMessage($chatId, "❌ Неверный код подключения.");
                return response()->json(['ok' => true]);
            }

            $connectUser->update([
                'telegram_chat_id' => $chatId,
                'telegram_connect_code' => null,
            ]);

            $this->sendMessage($chatId,
                "✅ Telegram успешно подключён к вашему аккаунту!\n\nТеперь доступны команды:\n/birthdays"
            );

            return response()->json(['ok' => true]);
        }

        // Команды для подключённого пользователя
        if ($text === '/birthdays') {
            $this->sendBirthdays($chatId);
            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => true]);
    }

        /*
        |--------------------------------------------------------------------------
        | 3. Если пользователь НЕ подключён — ожидаем код
        |--------------------------------------------------------------------------
        */

        if (!$user) {

            $userByCode = User::where('telegram_connect_code', $text)->first();

            if (!$userByCode) {
                $this->sendMessage($chatId, "❌ Неверный код подключения.");
                return response()->json(['ok' => true]);
            }

            // Подключаем
            $userByCode->telegram_chat_id = $chatId;
            $userByCode->telegram_connect_code = null;
            $userByCode->save();

            $this->sendMessage($chatId,
                "✅ Telegram успешно подключён к вашему аккаунту!\n\n" .
                "Доступные команды:\n" .
                "/birthdays — ближайшие дни рождения"
            );

            return response()->json(['ok' => true]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Обработка команд для подключённого пользователя
        |--------------------------------------------------------------------------
        */

        if ($text === '/birthdays') {
            $this->sendBirthdays($user, $chatId);
            return response()->json(['ok' => true]);
        }

        $this->sendMessage($chatId, "Команда не распознана.");
        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | Ближайшие дни рождения
    |--------------------------------------------------------------------------
    */

    private function sendBirthdays($user, $chatId)
    {
        $today = now();
        $in7   = now()->copy()->addDays(7);

        $people = Person::whereNotNull('birth_date')->get();

        $upcoming = $people->filter(function ($person) use ($today, $in7) {

            $birthday = Carbon::parse($person->birth_date)
                ->year($today->year);

            return $birthday->between($today, $in7);
        });

        if ($upcoming->isEmpty()) {
            $this->sendMessage($chatId, "🎂 В ближайшие 7 дней дней рождения нет.");
            return;
        }

        $message = "🎂 Ближайшие дни рождения:\n\n";

        foreach ($upcoming as $person) {

            $birthDate = Carbon::parse($person->birth_date);
            $birthdayThisYear = $birthDate->year($today->year);

            $age = $today->year - $birthDate->year;

            $message .= "• {$person->first_name} {$person->last_name}\n";
            $message .= "  📅 " . $birthdayThisYear->format('d.m') . "\n";
            $message .= "  🎈 Исполняется " . $this->formatYears($age) . "\n\n";
        }

        $this->sendMessage($chatId, $message);
    }

    /*
    |--------------------------------------------------------------------------
    | Правильное склонение "год"
    |--------------------------------------------------------------------------
    */

    private function formatYears($age)
    {
        $mod10 = $age % 10;
        $mod100 = $age % 100;

        if ($mod10 == 1 && $mod100 != 11) {
            return $age . " год";
        }

        if ($mod10 >= 2 && $mod10 <= 4 && !($mod100 >= 12 && $mod100 <= 14)) {
            return $age . " года";
        }

        return $age . " лет";
    }

    /*
    |--------------------------------------------------------------------------
    | Отправка сообщения
    |--------------------------------------------------------------------------
    */

    private function sendMessage($chatId, $text)
    {
        $token = config('services.telegram.bot_token');

        file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?" . http_build_query([
                'chat_id' => $chatId,
                'text' => $text,
            ]));
    }
}
