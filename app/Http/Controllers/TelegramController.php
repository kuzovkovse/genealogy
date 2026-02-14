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

        $user = User::where('telegram_chat_id', $chatId)->first();

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Если пользователь НЕ подключён
        |--------------------------------------------------------------------------
        */
        if (!$user) {

            if ($text === '/start' || $text === '/старт') {
                $this->sendMessage(
                    $chatId,
                    "👋 Добро пожаловать в ПомниКорни!\n\nВведите код подключения из вашего профиля."
                );
                return response()->json(['ok' => true]);
            }

            $userByCode = User::where('telegram_connect_code', $text)->first();

            if ($userByCode) {

                $userByCode->telegram_chat_id = $chatId;
                $userByCode->telegram_connect_code = null;
                $userByCode->save();

                $this->sendMessage(
                    $chatId,
                    "✅ Telegram успешно подключён!\n\nВыберите действие:",
                    $this->mainKeyboard()
                );

            } else {
                $this->sendMessage($chatId, "❌ Неверный код подключения.");
            }

            return response()->json(['ok' => true]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Пользователь подключён — команды
        |--------------------------------------------------------------------------
        */

        switch (mb_strtolower($text)) {

            case '/start':
            case '/старт':
                $this->sendMessage(
                    $chatId,
                    "👋 Вы подключены к ПомниКорни.\n\nВыберите действие:",
                    $this->mainKeyboard()
                );
                break;

            case '🎂 сегодня':
            case '/сегодня':
                $this->sendTodayBirthdays($chatId);
                break;

            case '📅 неделя':
            case '/неделя':
                $this->sendWeekBirthdays($chatId);
                break;

            case '📆 месяц':
            case '/месяц':
                $this->sendMonthBirthdays($chatId);
                break;

            case '⚙ настройки':
            case '/настройки':
                $this->sendMessage(
                    $chatId,
                    "⚙ Настройки:\n\n/отключить — отвязать Telegram",
                    $this->mainKeyboard()
                );
                break;

            case '/отключить':
                $user->telegram_chat_id = null;
                $user->save();

                $this->sendMessage(
                    $chatId,
                    "🔌 Telegram отключён от вашего аккаунта.\n\nЧтобы подключить снова — введите код."
                );
                break;

            default:
                $this->sendMessage(
                    $chatId,
                    "Неизвестная команда.\n\nВыберите действие:",
                    $this->mainKeyboard()
                );
        }

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 🎂 Сегодня
    |--------------------------------------------------------------------------
    */

    private function sendTodayBirthdays($chatId)
    {
        $today = Carbon::today();

        $people = Person::whereNotNull('birth_date')->get();

        $todayBirthdays = $people->filter(function ($person) use ($today) {
            $birth = Carbon::parse($person->birth_date);
            return $birth->day === $today->day &&
                $birth->month === $today->month;
        });

        if ($todayBirthdays->isEmpty()) {
            $this->sendMessage($chatId, "🎂 Сегодня дней рождения нет.", $this->mainKeyboard());
            return;
        }

        $message = "🎉 Сегодня день рождения:\n\n";

        foreach ($todayBirthdays as $person) {
            $birth = Carbon::parse($person->birth_date);
            $age   = $today->year - $birth->year;

            $message .= "• {$person->first_name} {$person->last_name}\n";
            $message .= "  🎂 {$age} " . $this->plural($age) . "\n\n";
        }

        $this->sendMessage($chatId, $message, $this->mainKeyboard());
    }

    /*
    |--------------------------------------------------------------------------
    | 📅 Неделя
    |--------------------------------------------------------------------------
    */

    private function sendWeekBirthdays($chatId)
    {
        $today = Carbon::today();
        $in7   = Carbon::today()->addDays(7);

        $people = Person::whereNotNull('birth_date')->get();

        $upcoming = $people->filter(function ($person) use ($today, $in7) {
            $birth = Carbon::parse($person->birth_date)->year($today->year);
            return $birth->between($today, $in7);
        });

        if ($upcoming->isEmpty()) {
            $this->sendMessage($chatId, "📅 В ближайшие 7 дней дней рождения нет.", $this->mainKeyboard());
            return;
        }

        $message = "📅 Ближайшие дни рождения:\n\n";

        foreach ($upcoming as $person) {
            $birth = Carbon::parse($person->birth_date);
            $birthday = $birth->year($today->year);
            $age = $today->year - $birth->year;

            $message .= "• {$person->first_name} {$person->last_name}\n";
            $message .= "  📅 " . $birthday->format('d.m') . "\n";
            $message .= "  🎂 {$age} " . $this->plural($age) . "\n\n";
        }

        $this->sendMessage($chatId, $message, $this->mainKeyboard());
    }

    /*
    |--------------------------------------------------------------------------
    | 📆 Месяц
    |--------------------------------------------------------------------------
    */

    private function sendMonthBirthdays($chatId)
    {
        $today = Carbon::today();
        $in30  = Carbon::today()->addDays(30);

        $people = Person::whereNotNull('birth_date')->get();

        $upcoming = $people->filter(function ($person) use ($today, $in30) {
            $birth = Carbon::parse($person->birth_date)->year($today->year);
            return $birth->between($today, $in30);
        });

        if ($upcoming->isEmpty()) {
            $this->sendMessage($chatId, "📆 В ближайшие 30 дней дней рождения нет.", $this->mainKeyboard());
            return;
        }

        $message = "📆 Дни рождения в ближайший месяц:\n\n";

        foreach ($upcoming as $person) {
            $birth = Carbon::parse($person->birth_date);
            $birthday = $birth->year($today->year);
            $age = $today->year - $birth->year;

            $message .= "• {$person->first_name} {$person->last_name}\n";
            $message .= "  📅 " . $birthday->format('d.m') . "\n";
            $message .= "  🎂 {$age} " . $this->plural($age) . "\n\n";
        }

        $this->sendMessage($chatId, $message, $this->mainKeyboard());
    }

    /*
    |--------------------------------------------------------------------------
    | 🔤 Склонение возраста
    |--------------------------------------------------------------------------
    */

    private function plural($age)
    {
        if ($age % 10 == 1 && $age % 100 != 11) return 'год';
        if (in_array($age % 10, [2,3,4]) && !in_array($age % 100, [12,13,14])) return 'года';
        return 'лет';
    }

    /*
    |--------------------------------------------------------------------------
    | 🎛 Главное меню
    |--------------------------------------------------------------------------
    */

    private function mainKeyboard()
    {
        return [
            'keyboard' => [
                [
                    ['text' => '🎂 Сегодня'],
                    ['text' => '📅 Неделя'],
                ],
                [
                    ['text' => '📆 Месяц'],
                    ['text' => '⚙ Настройки']
                ]
            ],
            'resize_keyboard' => true,
            'persistent' => true
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 📤 Отправка сообщения
    |--------------------------------------------------------------------------
    */

    private function sendMessage($chatId, $text, $keyboard = null)
    {
        $token = config('services.telegram.bot_token');

        $params = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($keyboard) {
            $params['reply_markup'] = json_encode($keyboard);
        }

        file_get_contents(
            "https://api.telegram.org/bot{$token}/sendMessage?" .
            http_build_query($params)
        );
    }
}
