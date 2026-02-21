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
        | 1️⃣ Пользователь НЕ подключён
        |--------------------------------------------------------------------------
        */
        if (!$user) {

            if ($text === '/start' || $text === '/старт') {
                $this->sendMessage(
                    $chatId,
                    "👋 *Добро пожаловать в ПомниКорни!*\n\nВведите код подключения из вашего профиля.",
                    null,
                    true
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
                    "✅ *Telegram успешно подключён!*\n\nВыберите действие:",
                    $this->mainKeyboard(),
                    true
                );
            } else {
                $this->sendMessage($chatId, "❌ Неверный код подключения.");
            }

            return response()->json(['ok' => true]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Пользователь подключён
        |--------------------------------------------------------------------------
        */

        switch (mb_strtolower($text)) {

            case '/start':
            case '/старт':
                $this->sendMessage(
                    $chatId,
                    "👋 Вы подключены к *ПомниКорни*.\n\nВыберите действие:",
                    $this->mainKeyboard(),
                    true
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

            case '📊 статистика':
            case '/статистика':
                $this->sendFamilyStats($chatId);
                break;

            case '🏛 факт дня':
            case '/факт':
                $this->sendHistoricalFact($chatId);
                break;

            case '⚙ настройки':
                $this->sendMessage(
                    $chatId,
                    "⚙ *Настройки*\n\n/отключить — отвязать Telegram",
                    $this->mainKeyboard(),
                    true
                );
                break;

            case '/отключить':
                $user->telegram_chat_id = null;
                $user->save();

                $this->sendMessage(
                    $chatId,
                    "🔌 Telegram отключён.\n\nЧтобы подключить снова — введите код."
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

        // Определяем тип списка
        $allAlive = $todayBirthdays->every(fn($p) => !$p->death_date);
        $allDead  = $todayBirthdays->every(fn($p) => $p->death_date);

        if ($allAlive) {
            $message = "🎉 *Сегодня день рождения:*\n\n";
        } elseif ($allDead) {
            $message = "🕯 *Сегодня памятная дата:*\n\n";
        } else {
            $message = "🎂 *Сегодня памятные даты:*\n\n";
        }

        foreach ($todayBirthdays as $person) {

            $age = $this->calculateTurningAge($person);

            $message .= "• *{$person->first_name} {$person->last_name}*\n";

            if ($person->death_date) {
                $message .= "  🕯 Исполнилось бы {$age} " . $this->plural($age) . "\n\n";
            } else {
                $message .= "  🎂 {$age} " . $this->plural($age) . "\n\n";
            }
        }

        $this->sendMessage($chatId, $message, $this->mainKeyboard(), true);
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

        $message = "📅 *Ближайшие дни рождения:*\n\n";

        foreach ($upcoming as $person) {
            $birth = Carbon::parse($person->birth_date);
            $birthday = $birth->year($today->year);
            $age = $this->calculateTurningAge($person);

            $message .= "• *{$person->first_name} {$person->last_name}*\n";
            $message .= "  📅 " . $birthday->format('d.m') . "\n";

            if ($person->death_date) {
                $message .= "  🕯 Исполнилось бы {$age} " . $this->plural($age) . "\n\n";
            } else {
                $message .= "  🎂 {$age} " . $this->plural($age) . "\n\n";
            }
        }

        $this->sendMessage($chatId, $message, $this->mainKeyboard(), true);
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
            $this->sendMessage($chatId, "📆 В ближайший месяц дней рождения нет.", $this->mainKeyboard());
            return;
        }

        $message = "📆 *Дни рождения в ближайший месяц:*\n\n";

        foreach ($upcoming as $person) {
            $birth = Carbon::parse($person->birth_date);
            $birthday = $birth->year($today->year);
            $age = $this->calculateTurningAge($person);

            $message .= "• *{$person->first_name} {$person->last_name}*\n";
            $message .= "  📅 " . $birthday->format('d.m') . "\n";

            if ($person->death_date) {
                $message .= "  🕯 Исполнилось бы {$age} " . $this->plural($age) . "\n\n";
            } else {
                $message .= "  🎂 {$age} " . $this->plural($age) . "\n\n";
            }
        }

        $this->sendMessage($chatId, $message, $this->mainKeyboard(), true);
    }

    /*
    |--------------------------------------------------------------------------
    | 📊 Статистика рода
    |--------------------------------------------------------------------------
    */

    private function sendFamilyStats($chatId)
    {
        $total = Person::count();
        $alive = Person::whereNull('death_date')->count();
        $deceased = Person::whereNotNull('death_date')->count();
        $men = Person::where('gender', 'male')->count();
        $women = Person::where('gender', 'female')->count();

        $message = "📊 *Статистика рода*\n\n";
        $message .= "👥 Всего людей: *{$total}*\n";
        $message .= "❤️ Живых: *{$alive}*\n";
        $message .= "🕯 Ушедших: *{$deceased}*\n";
        $message .= "👨 Мужчин: *{$men}*\n";
        $message .= "👩 Женщин: *{$women}*";

        $this->sendMessage($chatId, $message, $this->mainKeyboard(), true);
    }

    /*
    |--------------------------------------------------------------------------
    | 🏛 Исторический факт дня
    |--------------------------------------------------------------------------
    */

    private function sendHistoricalFact($chatId)
    {
        $facts = [
            "В древности родословные хранились устно и передавались поколениями.",
            "В России метрические книги начали вести с XVIII века.",
            "Самое длинное генеалогическое древо в мире насчитывает более 80 поколений.",
            "Фамилии в России стали массово использоваться только к XIX веку.",
            "Родовые книги дворян велись официально государством."
        ];

        $fact = $facts[array_rand($facts)];

        $this->sendMessage(
            $chatId,
            "🏛 *Исторический факт дня*\n\n{$fact}",
            $this->mainKeyboard(),
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 🔤 Склонение
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
                    ['text' => '📊 Статистика'],
                ],
                [
                    ['text' => '🏛 Факт дня'],
                    ['text' => '⚙ Настройки'],
                ]
            ],
            'resize_keyboard' => true,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 📤 Отправка сообщения
    |--------------------------------------------------------------------------
    */

    private function calculateTurningAge(Person $person)
    {
        if (!$person->birth_date) {
            return null;
        }

        $birth = Carbon::parse($person->birth_date);
        $today = Carbon::today();

        // День рождения в этом году
        $nextBirthday = $birth->copy()->year($today->year);

        // Если уже прошёл — следующий год
        if ($nextBirthday->lt($today)) {
            $nextBirthday->addYear();
        }

        // Сколько исполнится
        return $nextBirthday->year - $birth->year;
    }

    private function sendMessage($chatId, $text, $keyboard = null, $markdown = false)
    {
        $token = config('services.telegram.bot_token');

        $params = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($keyboard) {
            $params['reply_markup'] = json_encode($keyboard);
        }

        if ($markdown) {
            $params['parse_mode'] = 'Markdown';
        }

        file_get_contents(
            "https://api.telegram.org/bot{$token}/sendMessage?" .
            http_build_query($params)
        );
    }
}
